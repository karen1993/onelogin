
------------------------------------------------------------
-- apex_pagina_tipo
------------------------------------------------------------
INSERT INTO apex_pagina_tipo (proyecto, pagina_tipo, descripcion, clase_nombre, clase_archivo, include_arriba, include_abajo, exclusivo_toba, contexto, punto_montaje) VALUES (
	'onelogin', --proyecto
	'ini', --pagina_tipo
	'inicio login comun', --descripcion
	'toba_onelog_uncoma', --clase_nombre
	'toba_onelog_uncoma.php', --clase_archivo
	NULL, --include_arriba
	NULL, --include_abajo
	NULL, --exclusivo_toba
	NULL, --contexto
	'1000004'  --punto_montaje
);
INSERT INTO apex_pagina_tipo (proyecto, pagina_tipo, descripcion, clase_nombre, clase_archivo, include_arriba, include_abajo, exclusivo_toba, contexto, punto_montaje) VALUES (
	'onelogin', --proyecto
	'onelog User', --pagina_tipo
	'oneLogin Usuario', --descripcion
	'toba_onelog_usuario', --clase_nombre
	'toba_onelog_usuario.php', --clase_archivo
	NULL, --include_arriba
	NULL, --include_abajo
	NULL, --exclusivo_toba
	NULL, --contexto
	'1000004'  --punto_montaje
);
