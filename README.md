# Dev Day 2025 - Auto Orden

## Descripción

Este plugin permite a los clientes de WooCommerce habilitar pedidos automáticos en el checkout. Cuando el usuario marca
la opción "Repetir esta compra cada mes", el sistema creará un nuevo pedido automáticamente cada mes con los mismos
productos.

## Antes del taller

- Sitio local corriendo WordPress  (Servidor recomendado: https://localwp.com/) (minimo PHP 7.4+)
- Instalar  [WooCommerce](https://wordpress.org/plugins/woocommerce/).
- Importar datos de ejemplo   [Decargar Aqui](https://woocommerce.com/document/importing-woocommerce-sample-data/)
- Activar al menos un metodo de pago (WooCommerce > Settings > Payments)
- Probar proceso de checkout
- Instalar plugin  [query-monitor](https://wordpress.org/plugins/query-monitor/)
- Instalar [Composer](https://getcomposer.org/download/)
- Instalar UI para manejar la DB (Adminer, PhpMyAdmin, etc)(Local By Flywheel ya trae Adminer)
- Instalar [wc-smooth-generator](https://github.com/woocommerce/wc-smooth-generator) para generar datos de prueba
- (Opcional) Installar Xdebug ([Guia para Local By Flywheel](https://localwp.com/help-docs/advanced/using-xdebug-within-local/))

## Reglas de negocio

- [x] Si el cliente marca el checkbox en el checkout, el pedido se guarda con la meta `repeat_order = yes`.
- [ ] El cron job se ejecuta diariamente para revisar pedidos con `repeat_order = yes`.
- [ ] Se debe verificar la disponibilidad de stock antes de crear un pedido automático.
- [ ] Se debe notificar al cliente por correo electrónico cuando se genere un pedido automático.
- [ ] Agregar nota en el pedido para indicar que es una renovación automática.
- [ ] En la página "Mi Cuenta", los clientes deben poder visualizar sus suscripciones activas y cancelarlas.
- [ ] Si un cliente cancela su suscripción, se debe eliminar la meta `repeat_order` y detener la programación de nuevos pedidos (historial, quiero saber que era una subscription)
- [ ] Agregar una columna en el admin para mostrar si un pedido es recurrente.
- [ ] Agregar un filtro en el admin para mostrar solo pedidos recurrentes.
- [ ] Enviar de cancelacion

## Notas
- Para agregar el meta de manera random a ordenes (https://gist.github.com/rcstr/5d244526482f227b38f93c4df35fbf5a)
