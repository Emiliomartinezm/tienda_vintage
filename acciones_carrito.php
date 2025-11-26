<?php
session_start();
require 'db.php';

// Si no hay usuario logueado, mandarlo al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

// --- 1. AGREGAR PRODUCTO AL CARRITO ---
if ($accion === 'agregar') {
    $id_producto = (int)$_POST['id_producto'];
    $cantidad    = (int)$_POST['cantidad'];

    // Verificar si el usuario ya tiene un carrito "abierto"
    $query = "SELECT id FROM carritos WHERE id_usuario = ? AND estado = 'abierto'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $carrito = mysqli_fetch_assoc($res);

    if (!$carrito) {
        // Si no tiene carrito, creamos uno
        $insert = "INSERT INTO carritos (id_usuario, estado) VALUES (?, 'abierto')";
        $stmt_ins = mysqli_prepare($conn, $insert);
        mysqli_stmt_bind_param($stmt_ins, "i", $usuario_id);
        mysqli_stmt_execute($stmt_ins);
        $id_carrito = mysqli_insert_id($conn);
    } else {
        $id_carrito = $carrito['id'];
    }

    // Verificar precio actual del producto
    $q_prod = "SELECT precio FROM productos WHERE id = ?";
    $stmt_p = mysqli_prepare($conn, $q_prod);
    mysqli_stmt_bind_param($stmt_p, "i", $id_producto);
    mysqli_stmt_execute($stmt_p);
    $prod_data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_p));
    $precio = $prod_data['precio'];

    // Verificar si el producto ya está en el carrito
    $q_item = "SELECT id, cantidad FROM carrito_items WHERE id_carrito = ? AND id_producto = ?";
    $stmt_i = mysqli_prepare($conn, $q_item);
    mysqli_stmt_bind_param($stmt_i, "ii", $id_carrito, $id_producto);
    mysqli_stmt_execute($stmt_i);
    $item_existente = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_i));

    if ($item_existente) {
        // Actualizar cantidad
        $nueva_cant = $item_existente['cantidad'] + $cantidad;
        $upd = "UPDATE carrito_items SET cantidad = ? WHERE id = ?";
        $stmt_u = mysqli_prepare($conn, $upd);
        mysqli_stmt_bind_param($stmt_u, "ii", $nueva_cant, $item_existente['id']);
        mysqli_stmt_execute($stmt_u);
    } else {
        // Insertar nuevo item
        $ins = "INSERT INTO carrito_items (id_carrito, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
        $stmt_in = mysqli_prepare($conn, $ins);
        mysqli_stmt_bind_param($stmt_in, "iiid", $id_carrito, $id_producto, $cantidad, $precio);
        mysqli_stmt_execute($stmt_in);
    }

    header('Location: carrito.php');
    exit;
}

// --- 2. ELIMINAR ITEM DEL CARRITO ---
if ($accion === 'eliminar') {
    $id_item = (int)$_POST['id_item'];
    // Aseguramos que el item pertenezca a un carrito del usuario actual (seguridad)
    $del = "DELETE ci FROM carrito_items ci 
            JOIN carritos c ON ci.id_carrito = c.id 
            WHERE ci.id = ? AND c.id_usuario = ?";
    $stmt = mysqli_prepare($conn, $del);
    mysqli_stmt_bind_param($stmt, "ii", $id_item, $usuario_id);
    mysqli_stmt_execute($stmt);
    
    header('Location: carrito.php');
    exit;
}

// --- 3. FINALIZAR COMPRA (Checkout) ---
if ($accion === 'comprar') {
    // Buscar carrito abierto
    $q = "SELECT id FROM carritos WHERE id_usuario = ? AND estado = 'abierto'";
    $stmt = mysqli_prepare($conn, $q);
    mysqli_stmt_bind_param($stmt, "i", $usuario_id);
    mysqli_stmt_execute($stmt);
    $carrito = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($carrito) {
        $id_carrito = $carrito['id'];

        // Obtener items para verificar stock y calcular total
        $q_items = "SELECT ci.id_producto, ci.cantidad, ci.precio_unitario, p.stock, p.nombre 
                    FROM carrito_items ci
                    JOIN productos p ON ci.id_producto = p.id
                    WHERE ci.id_carrito = ?";
        $stmt_items = mysqli_prepare($conn, $q_items);
        mysqli_stmt_bind_param($stmt_items, "i", $id_carrito);
        mysqli_stmt_execute($stmt_items);
        $res_items = mysqli_stmt_get_result($stmt_items);
        
        $total_compra = 0;
        $items_para_procesar = [];

        // Validar Stock antes de nada
        while ($row = mysqli_fetch_assoc($res_items)) {
            if ($row['stock'] < $row['cantidad']) {
                die("Error: No hay suficiente stock para el producto: " . $row['nombre']);
            }
            $total_compra += ($row['precio_unitario'] * $row['cantidad']);
            $items_para_procesar[] = $row;
        }

        if (empty($items_para_procesar)) {
            header('Location: carrito.php'); // Carrito vacío
            exit;
        }

        // --- INICIO DE TRANSACCIÓN (Para que todo se haga o nada se haga) ---
        mysqli_begin_transaction($conn);

        try {
            // 1. Crear el Pedido
            $ins_pedido = "INSERT INTO pedidos (id_usuario, total, estado) VALUES (?, ?, 'pagado')";
            $stmt_p = mysqli_prepare($conn, $ins_pedido);
            mysqli_stmt_bind_param($stmt_p, "id", $usuario_id, $total_compra);
            mysqli_stmt_execute($stmt_p);
            $id_pedido = mysqli_insert_id($conn);

            // 2. Mover items al pedido y BAJAR STOCK
            foreach ($items_para_procesar as $item) {
                // Insertar en pedido_items
                $ins_pi = "INSERT INTO pedido_items (id_pedido, id_producto, nombre_producto, precio_unitario, cantidad) VALUES (?, ?, ?, ?, ?)";
                $stmt_pi = mysqli_prepare($conn, $ins_pi);
                mysqli_stmt_bind_param($stmt_pi, "iisdi", $id_pedido, $item['id_producto'], $item['nombre'], $item['precio_unitario'], $item['cantidad']);
                mysqli_stmt_execute($stmt_pi);

                // Actualizar Stock (Restar)
                $upd_stock = "UPDATE productos SET stock = stock - ? WHERE id = ?";
                $stmt_s = mysqli_prepare($conn, $upd_stock);
                mysqli_stmt_bind_param($stmt_s, "ii", $item['cantidad'], $item['id_producto']);
                mysqli_stmt_execute($stmt_s);
            }

            // 3. Cerrar el carrito (o vaciarlo)
            // Opción A: Marcar carrito como 'comprado'
            $upd_c = "UPDATE carritos SET estado = 'comprado' WHERE id = ?";
            $stmt_uc = mysqli_prepare($conn, $upd_c);
            mysqli_stmt_bind_param($stmt_uc, "i", $id_carrito);
            mysqli_stmt_execute($stmt_uc);

            // Opción B: También podemos borrar los items del carrito para limpiarlo si reutilizamos el ID,
            // pero como cambiamos el estado a 'comprado', el sistema creará uno nuevo 'abierto' la próxima vez.

            mysqli_commit($conn);
            
            // Redirigir con éxito (puedes crear una pagina de gracias)
            header('Location: catalogo.php?mensaje=compra_exitosa');
            exit;

        } catch (Exception $e) {
            mysqli_rollback($conn);
            die("Error en la compra: " . $e->getMessage());
        }
    }
}

// Si llega aquí sin acción
header('Location: index.php');
?>