-- Sequence: portal.portal_concurso_cod_portal_concurso_seq

-- DROP SEQUENCE portal.portal_concurso_cod_portal_concurso_seq;

CREATE SEQUENCE portal.anexos_formacao_cod_anexos_formacao_seq
  INCREMENT 1
  MINVALUE 0
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;
ALTER TABLE portal.anexos_formacao_cod_anexos_formacao_seq
  OWNER TO ieducar;

-- Table: portal.anexos_formacao

-- DROP TABLE portal.anexos_formacao;

CREATE TABLE portal.anexos_formacao
(
  cod_anexos_formacao integer NOT NULL DEFAULT nextval('anexos_formacao_cod_anexos_formacao_seq'::regclass),
  ref_ref_cod_pessoa_fj integer NOT NULL DEFAULT 0,
  nm_concurso character varying(255) NOT NULL DEFAULT ''::character varying,
  descricao text,
  caminho character varying(255) NOT NULL DEFAULT ''::character varying,
  tipo_arquivo character(3) NOT NULL DEFAULT ''::bpchar,
  data_hora timestamp without time zone,
  CONSTRAINT anexos_formacao_pk PRIMARY KEY (cod_anexos_formacao ),
  CONSTRAINT anexos_formacao_ibfk_1 FOREIGN KEY (ref_ref_cod_pessoa_fj)
      REFERENCES portal.funcionario (ref_cod_pessoa_fj) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
)
WITH (
  OIDS=TRUE
);
ALTER TABLE portal.anexos_formacao
  OWNER TO ieducar;

-- Trigger: RI_ConstraintTrigger_71026 on portal.anexos_formacao

-- DROP TRIGGER "RI_ConstraintTrigger_71026" ON portal.anexos_formacao;

CREATE CONSTRAINT TRIGGER "RI_ConstraintTrigger_71026"
  AFTER INSERT
  ON portal.anexos_formacao
  FOR EACH ROW
  EXECUTE PROCEDURE "RI_FKey_check_ins"('anexos_formacao_ibfk_1', 'anexos_formacao', 'funcionario', 'UNSPECIFIED', 'ref_ref_cod_pessoa_fj', 'ref_cod_pessoa_fj');

-- Trigger: RI_ConstraintTrigger_71027 on portal.anexos_formacao

-- DROP TRIGGER "RI_ConstraintTrigger_71027" ON portal.anexos_formacao;

CREATE CONSTRAINT TRIGGER "RI_ConstraintTrigger_71027"
  AFTER UPDATE
  ON portal.anexos_formacao
  FOR EACH ROW
  EXECUTE PROCEDURE "RI_FKey_check_upd"('anexos_formacao_ibfk_1', 'anexos_formacao', 'funcionario', 'UNSPECIFIED', 'ref_ref_cod_pessoa_fj', 'ref_cod_pessoa_fj');
