INSERT INTO `ps_order_state` (`id_order_state`, `invoice`, `send_email`, `module_name`, `color`, `unremovable`, `hidden`, `logable`, `delivery`, `shipped`, `paid`, `pdf_invoice`, `pdf_delivery`, `deleted`) VALUES
(900, 0, 0, 'correosoficial', '#F4CD06', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(901, 0, 0, 'correosoficial', '#F4CD06', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(902, 0, 0, 'correosoficial', '#F4CD06', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(903, 0, 0, 'correosoficial', '#F4CD06', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(904, 0, 0, 'correosoficial', '#F4CD06', 0, 0, 0, 0, 0, 0, 0, 0, 0);

INSERT INTO `ps_order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`) VALUES
(900, 1, 'Envío preparado para Correos - CEX', ''),
(900, 2, 'Envío preparado para Correos - CEX', ''),
(900, 3, 'Envío preparado para Correos - CEX', ''),
(900, 4, 'Envío preparado para Correos - CEX', ''),
(900, 5, 'Envío preparado para Correos - CEX', ''),
(901, 1, 'Envío anulado Correos - CEX', ''),
(901, 2, 'Envío anulado Correos - CEX', ''),
(901, 3, 'Envío anulado Correos - CEX', ''),
(901, 4, 'Envío anulado Correos - CEX', ''),
(901, 5, 'Envío anulado Correos - CEX', ''),
(902, 1, 'Envío devuelto Correos - CEX', ''),
(902, 2, 'Envío devuelto Correos - CEX', ''),
(902, 3, 'Envío devuelto Correos - CEX', ''),
(902, 4, 'Envío devuelto Correos - CEX', ''),
(902, 5, 'Envío devuelto Correos - CEX', ''),
(903, 1, 'Envío entregado Correos - CEX', ''),
(903, 2, 'Envío entregado Correos - CEX', ''),
(903, 3, 'Envío entregado Correos - CEX', ''),
(903, 4, 'Envío entregado Correos - CEX', ''),
(903, 5, 'Envío entregado Correos - CEX', ''),
(904, 1, 'Envío en curso Correos - CEX', ''),
(904, 2, 'Envío en curso Correos - CEX', ''),
(904, 3, 'Envío en curso Correos - CEX', ''),
(904, 4, 'Envío en curso Correos - CEX', ''),
(904, 5, 'Envío en curso Correos - CEX', '');


INSERT INTO `ps_order_return_state` (`id_order_return_state`, `color`) VALUES
(900, '#F4CD06');

INSERT INTO `ps_order_return_state_lang` (`id_order_return_state`, `id_lang`, `name`) VALUES
(900, 1, 'Envío devuelto Correos - CEX'),
(900, 2, 'Envío devuelto Correos - CEX'),
(900, 3, 'Envío devuelto Correos - CEX'),
(900, 4, 'Envío devuelto Correos - CEX'),
(900, 5, 'Envío devuelto Correos - CEX');