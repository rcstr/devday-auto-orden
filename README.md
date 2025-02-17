# Dev Day 2025 - Auto Orden

## Descripción

Este plugin permite a los clientes de WooCommerce habilitar pedidos automáticos en el checkout. Cuando el usuario marca
la opción "Repetir esta compra cada mes", el sistema creará un nuevo pedido automáticamente cada mes con los mismos
productos.

## Reglas de negocio

- Si el cliente marca el checkbox en el checkout, el pedido se guarda con la meta repeat_order = yes.
- El cron job se ejecuta diariamente para revisar pedidos con repeat_order = yes.
- Se debe verificar la disponibilidad de stock antes de crear un pedido automático.
- Se debe notificar al cliente por correo electrónico cuando se genere un pedido automático.
- En la página "Mi Cuenta", los clientes deben poder visualizar sus suscripciones activas y cancelarlas.
- Si un cliente cancela su suscripción, se debe eliminar la meta repeat_order y detener la programación de nuevos
  pedidos.
- Agregar una columna en el admin para mostrar si un pedido es recurrente.

## Antes del taller

- Sitio local corriendo WordPress  (Servidor recomendado: https://localwp.com/) (minimo PHP 7.4+)
- Instalar  [WooCommerce](https://wordpress.org/plugins/woocommerce/).
- Importar datos de ejemplo   [Decargar Aqui](https://woocommerce.com/document/importing-woocommerce-sample-data/)
- Activar al menos un metodo de pago (WooCommerce > Settings > Payments)
- Probar proceso de checkout
- Instalar plugin  [query-monitor](https://wordpress.org/plugins/query-monitor/)
- Installar Xdebug ([Guia para Local By Flywheel](https://localwp.com/help-docs/advanced/using-xdebug-within-local/))

