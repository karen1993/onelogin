------------------------------------------------------------
--[1002000095]--  MostrarDatos - DR - solicitud_usuario 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1002
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'onelogin', --proyecto
	'1002000095', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_datos_tabla', --clase
	'1000004', --punto_montaje
	'dt_solicitud_usuario', --subclase
	'datos/dt_solicitud_usuario.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'MostrarDatos - DR - solicitud_usuario', --nombre
	NULL, --titulo
	NULL, --colapsable
	NULL, --descripcion
	'onelogin', --fuente_datos_proyecto
	'onelogin_solicitud', --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2020-06-09 11:41:50', --creacion
	NULL  --posicion_botonera
);
--- FIN Grupo de desarrollo 1002

------------------------------------------------------------
-- apex_objeto_db_registros
------------------------------------------------------------
INSERT INTO apex_objeto_db_registros (objeto_proyecto, objeto, max_registros, min_registros, punto_montaje, ap, ap_clase, ap_archivo, tabla, tabla_ext, alias, modificar_claves, fuente_datos_proyecto, fuente_datos, permite_actualizacion_automatica, esquema, esquema_ext) VALUES (
	'onelogin', --objeto_proyecto
	'1002000095', --objeto
	NULL, --max_registros
	NULL, --min_registros
	'1000004', --punto_montaje
	'1', --ap
	NULL, --ap_clase
	NULL, --ap_archivo
	'solicitud_usuario', --tabla
	NULL, --tabla_ext
	NULL, --alias
	'0', --modificar_claves
	'onelogin', --fuente_datos_proyecto
	'onelogin_solicitud', --fuente_datos
	'1', --permite_actualizacion_automatica
	NULL, --esquema
	'public'  --esquema_ext
);

------------------------------------------------------------
-- apex_objeto_db_registros_col
------------------------------------------------------------

--- INICIO Grupo de desarrollo 1002
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'onelogin', --objeto_proyecto
	'1002000095', --objeto
	'1002000357', --col_id
	'id_solicitud', --columna
	'E', --tipo
	'1', --pk
	'solicitud_usuario_id_solicitud_seq', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'solicitud_usuario'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'onelogin', --objeto_proyecto
	'1002000095', --objeto
	'1002000358', --col_id
	'nombre_usuario', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'solicitud_usuario'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'onelogin', --objeto_proyecto
	'1002000095', --objeto
	'1002000359', --col_id
	'id_estado', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	'4', --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'solicitud_usuario'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'onelogin', --objeto_proyecto
	'1002000095', --objeto
	'1002000360', --col_id
	'id_sistema', --columna
	'E', --tipo
	'0', --pk
	'solicitud_usuario_id_sistema_seq', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'solicitud_usuario'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'onelogin', --objeto_proyecto
	'1002000095', --objeto
	'1002000361', --col_id
	'id_perfil_datos', --columna
	'E', --tipo
	'0', --pk
	'solicitud_usuario_id_perfil_datos_seq', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'solicitud_usuario'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'onelogin', --objeto_proyecto
	'1002000095', --objeto
	'1002000362', --col_id
	'id_perfil_funcional', --columna
	'E', --tipo
	'0', --pk
	'solicitud_usuario_id_perfil_funcional_seq', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'1', --no_nulo_db
	'0', --externa
	'solicitud_usuario'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'onelogin', --objeto_proyecto
	'1002000095', --objeto
	'1002000363', --col_id
	'timestamp', --columna
	'T', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'solicitud_usuario'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'onelogin', --objeto_proyecto
	'1002000095', --objeto
	'1002000364', --col_id
	'nombre', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'solicitud_usuario'  --tabla
);
INSERT INTO apex_objeto_db_registros_col (objeto_proyecto, objeto, col_id, columna, tipo, pk, secuencia, largo, no_nulo, no_nulo_db, externa, tabla) VALUES (
	'onelogin', --objeto_proyecto
	'1002000095', --objeto
	'1002000365', --col_id
	'apellido', --columna
	'C', --tipo
	'0', --pk
	'', --secuencia
	NULL, --largo
	NULL, --no_nulo
	'0', --no_nulo_db
	'0', --externa
	'solicitud_usuario'  --tabla
);
--- FIN Grupo de desarrollo 1002
