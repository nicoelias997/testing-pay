<?php

return [
    'error' => 'Se ha producido un error en la aplicación. Contacte con el administrador de la aplicación para solucionarlo.',
    'errorConsumption' => 'Se ha producido un error en la aplicación con el consumo. Contacte con el administrador de la aplicación para solucionarlo.',
    'errorUpdatingMes' => 'Se ha producido un error en la aplicación con la actualización de los datos. Contacte con el administrador de la aplicación para solucionarlo.',
    'errors' => [
        'order_exists_mes' => 'No se encontró la orden :order en el sistema MES.',
        'machine_exists_mes' => 'No se pudo asignar la máquina :machine para la orden :order debido a que no existe en el sistema MES.',
        'duplicated_order_mes' => 'No se pudo cargar la orden :order debido a que ya existe en el sistema MES.',
        'process' => '{1} La orden :id no ha podido procesarse.|[2,*] Las órdenes no han sido procesadas.',
        'invalid_access_permission' => 'No tiene permisos para acceder a ningún panel de gestión. Contacte con el administrador de la aplicación para solucionarlo.',
    ],
    'auth' => [
        'invalid' => 'Credenciales invalidas'
    ],
    // Replace with 'errors'
    'invalid_access_permission' => 'No tiene permisos para acceder a ningún panel de gestión. Contacte con el administrador de la aplicación para solucionarlo.',
    'model' => [
        'created'       => 'Registro creado correctamente.',
        'updated'       => 'Registro actualizado correctamente.',
        'deleted'       => 'Registro eliminado correctamente.',
        // Unused?
        'error'         => 'Ha habido un error al aplicar la/s acción/es sobre el registro',
        'error_message' => 'Ha habido un error al aplicar la/s acción/es sobre el registro: :message',
    ],
    'order' => [
        'created'       => 'Orden creada correctamente.',
        'updated'       => 'Orden actualizada correctamente.',
        'deleted'       => 'Orden eliminada correctamente.',
        // Unused?
        'error'         => 'Ha habido un error al aplicar la/s acción/es sobre la orden',
        'error_message' => 'Ha habido un error al aplicar la/s acción/es sobre la orden: :message',
    ],
    'process' => [
        'success' => '{1} La orden :id se ha procesado correctamente.|[2,*] Las órdenes se han procesado correctamente.',
        // Replace with 'errors'
        'error'   => '{1} La orden :id no ha podido procesarse.|[2,*] Las órdenes no han sido procesadas.'
    ],
    'article' => [
        'success' => '{1} El articulo :code se ha procesado correctamente. |[2,*] Los articulos se han procesado correctamente.',
        'error'   => '{1} El articulo :code no ha podido procesarse.|[2,*] Los articulos no han sido procesadas.'
    ],
    'user' => [
        'created'       => 'Usuario creado correctamente.',
        'updated'       => 'Usuario actualizado correctamente.',
        'deleted'       => 'Usuario eliminado correctamente.',
        // Unused?
        'error'         => 'Ha habido un error al aplicar la/s acción/es sobre el usuario',
        'error_message' => 'Ha habido un error al aplicar la/s acción/es sobre el usuario: :message',
    ],
    "categories" => [
        "calidad" => "Calidad",
        "estampacion" =>  "Estampacion",
        "decolataje" => "Decolataje",
        "limpieza" =>  "Limpieza Piezas",
        "almacen" => "Almacen",
        "produccion" => "Produccion",
        "roscadora" => "Roscadora",
        "ingenieria" => "Ingenieria",
        "almacenModula"=> "Almacen Modula",
        "director"=> "Director de Fabrica",
        "gerencia"=> "Gerencia",
        "comercial"=> "Oficina Comercial",
        "logistica"=> "Oficina Logistica",
        "oficina"=> "Oficina",
        "gestionCalidad"=> "Gestion de Calidad",
        "calidadProcesos"=> "Calidad Procesos"
    ],
    "reason" => [
        "vacaciones" => "Vacaciones",
        "diaLibre" => "Dia/as Libre/es",
        "otro" => "Otros"
    ],   
    "notifications" => [
        "priority" => [
          "0" =>  "Alta",
          "1" =>  "Media",
          "2" => "Baja"
    ]
]
];
