Adicionar Campos:

ALTER TABLE pmieducar.quadro_horario_horarios ADD data_aula date;
ALTER TABLE pmieducar.quadro_horario_horarios ADD cod_quadro_horario_horarios integer NOT NULL DEFAULT nextval('quadro_horario_horarios_cod_quadro_horario_horarios_seq'::regclass);
ALTER TABLE pmieducar.quadro_horario_horarios ADD conteudo character varying(512);
	
CREATE SEQUENCE pmieducar.quadro_horario_horarios_cod_quadro_horario_horarios_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;