--
-- Name: vps_jornada_trabalho_cod_vps_jornada_trabalho_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.vps_jornada_trabalho_cod_vps_jornada_trabalho_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: vps_funcao_cod_vps_funcao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.vps_funcao_cod_vps_funcao_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: vps_idioma_cod_vps_idioma_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.vps_idioma_cod_vps_idioma_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: vps_responsavel_entrevista_cod_vps_responsavel_entrevista_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.vps_responsavel_entrevista_cod_vps_responsavel_entrevista_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: vps_tipo_contratacao_cod_vps_tipo_contratacao_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.vps_tipo_contratacao_cod_vps_tipo_contratacao_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: vps_entrevista_cod_vps_entrevista_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.vps_entrevista_cod_vps_entrevista_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: vps_aluno_entrevista_cod_vps_aluno_entrevista_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.vps_aluno_entrevista_cod_vps_aluno_entrevista_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: aluno_vps_cod_aluno_vps_seq; Type: SEQUENCE; Schema: pmieducar; Owner: -
--

CREATE SEQUENCE pmieducar.aluno_vps_cod_aluno_vps_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: pmieducar.vps_jornada_trabalho; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace:
--

CREATE TABLE pmieducar.vps_jornada_trabalho (
	cod_vps_jornada_trabalho integer DEFAULT nextval('vps_jornada_trabalho_cod_vps_jornada_trabalho_seq'::regclass) NOT NULL,
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	nm_jornada_trabalho character varying(255) NOT NULL,
	carga_horaria_semana double precision,
	carga_horaria_diaria double precision,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	ref_cod_instituicao integer
);

--
-- Name: pmieducar.vps_funcao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace:
--

CREATE TABLE pmieducar.vps_funcao (
	cod_vps_funcao integer DEFAULT nextval('vps_funcao_cod_vps_funcao_seq'::regclass) NOT NULL,
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	nm_funcao character varying(255) NOT NULL,
	descricao text,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	ref_cod_escola integer,
);

--
-- Name: pmieducar.vps_idioma; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace:
--

CREATE TABLE pmieducar.vps_idioma (
	cod_vps_idioma integer DEFAULT nextval('vps_idioma_cod_vps_idioma_seq'::regclass) NOT NULL,
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	nm_idioma character varying(255) NOT NULL,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	ref_cod_instituicao integer
);

--
-- Name: pmieducar.vps_responsavel_entrevista; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace:
--

CREATE TABLE pmieducar.vps_responsavel_entrevista (
	cod_vps_responsavel_entrevista integer DEFAULT nextval('vps_responsavel_entrevista_cod_vps_responsavel_entrevista_seq'::regclass) NOT NULL,
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	nm_responsavel character varying(255) NOT NULL,
	email character varying(50),
	ddd_telefone_com numeric(3,0),
	telefone_com numeric(11,0),
	ddd_telefone_cel numeric(3,0),
	telefone_cel numeric(11,0),
	observacao text,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	ref_cod_escola integer NOT NULL,
	ref_idpes integer NOT NULL
);

--
-- Name: pmieducar.vps_tipo_contratacao; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace:
--

CREATE TABLE pmieducar.vps_tipo_contratacao (
	cod_vps_tipo_contratacao integer DEFAULT nextval('vps_tipo_contratacao_cod_vps_tipo_contratacao_seq'::regclass) NOT NULL,
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	nm_tipo character varying(255) NOT NULL,
	descricao text,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	ref_cod_instituicao integer NOT NULL
);

--
-- Name: pmieducar.vps_entrevista; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace:
--

CREATE TABLE pmieducar.vps_entrevista (
	cod_vps_entrevista integer DEFAULT nextval('vps_entrevista_cod_vps_entrevista_seq'::regclass) NOT NULL,
	ref_cod_vps_entrevista integer,
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	salario double precision,
	data_entrevista date,
	hora_entrevista time without time zone,
	ref_idpes integer NOT NULL,
	ref_cod_vps_tipo_contratacao integer,
	ref_cod_vps_funcao integer,
	ref_cod_vps_jornada_trabalho integer,
	descricao text,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	ref_cod_escola integer NOT NULL,
	ref_cod_curso integer NOT NULL,
	ano integer NOT NULL,
	numero_vagas integer NOT NULL,
	numero_jovens integer NOT NULL
	situacao_entrevista integer DEFAULT (1)::integer NOT NULL,
	inicio_vps date,
	termino_vps date,
	insercao_vps date,
);

--
-- Name: pmieducar.vps_entrevista_idioma; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace:
--

CREATE TABLE pmieducar.vps_entrevista_idioma (
	ref_cod_vps_entrevista integer NOT NULL,
	ref_cod_vps_idioma integer NOT NULL
);

--
-- Name: pmieducar.vps_entrevista_responsavel; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace:
--

CREATE TABLE pmieducar.vps_entrevista_responsavel (
	ref_cod_vps_responsavel_entrevista integer NOT NULL,
	ref_cod_vps_entrevista integer NOT NULL,
	principal smallint NOT NULL DEFAULT (0)::smallint
);

--
-- Name: pmieducar.vps_aluno_entrevista; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace:
--

CREATE TABLE pmieducar.vps_aluno_entrevista (
	cod_vps_aluno_entrevista integer NOT NULL DEFAULT nextval('vps_aluno_entrevista_cod_vps_aluno_entrevista_seq'::regclass),
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	ref_cod_aluno integer NOT NULL,
	ref_cod_vps_entrevista integer NOT NULL,
	resultado_entrevista integer DEFAULT 0,
	inicio_vps date,
	termino_vps date,
	insercao_vps date,
	motivo_termino character varying(255)
);

--
-- Name: pmieducar.aluno_vps; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace:
--

CREATE TABLE pmieducar.aluno_vps (
	cod_aluno_vps integer NOT NULL DEFAULT nextval('aluno_vps_cod_aluno_vps_seq'::regclass),
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint DEFAULT (1)::smallint NOT NULL,
	ref_cod_aluno integer NOT NULL,
	situacao_vps integer DEFAULT 0,
	ref_cod_vps_aluno_entrevista integer NOT NULL,
	motivo_desligamento character varying(255),
	observacao character varying(512),
	prioridade integer
);

--
-- Name: i_responsavel_entrevista_ref_idpes; Type: INDEX; Schema: pmieducar; Owner: -; Tablespace:
--

CREATE INDEX i_responsavel_entrevista_ref_idpes ON pmieducar.vps_responsavel_entrevista USING btree (ref_idpes);

--
-- Name: vps_jornada_trabalho_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace:
--

ALTER TABLE ONLY pmieducar.vps_jornada_trabalho
	ADD CONSTRAINT vps_jornada_trabalho_pkey PRIMARY KEY (cod_vps_jornada_trabalho);

--
-- Name: vps_funcao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace:
--

ALTER TABLE ONLY pmieducar.vps_funcao
	ADD CONSTRAINT vps_funcao_pkey PRIMARY KEY (cod_vps_funcao);

--
-- Name: vps_idioma_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace:
--

ALTER TABLE ONLY pmieducar.vps_idioma
	ADD CONSTRAINT vps_idioma_pkey PRIMARY KEY (cod_vps_idioma);

--
-- Name: vps_responsavel_entrevista_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace:
--

ALTER TABLE ONLY pmieducar.vps_responsavel_entrevista
	ADD CONSTRAINT vps_responsavel_entrevista_pkey PRIMARY KEY (cod_vps_responsavel_entrevista);

--
-- Name: vps_tipo_contratacao_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace:
--

ALTER TABLE ONLY pmieducar.vps_tipo_contratacao
	ADD CONSTRAINT vps_tipo_contratacao_pkey PRIMARY KEY (cod_vps_tipo_contratacao);

--
-- Name: vps_entrevista_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace:
--

ALTER TABLE ONLY pmieducar.vps_entrevista
	ADD CONSTRAINT vps_entrevista_pkey PRIMARY KEY (cod_vps_entrevista);

--
-- Name: vps_entrevista_responsavel_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace:
--

ALTER TABLE ONLY pmieducar.vps_entrevista_responsavel
	ADD CONSTRAINT vps_entrevista_responsavel_pkey PRIMARY KEY (ref_cod_vps_responsavel_entrevista, ref_cod_vps_entrevista);

--
-- Name: vps_entrevista_idioma_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace:
--

ALTER TABLE ONLY pmieducar.vps_entrevista_idioma
	ADD CONSTRAINT vps_entrevista_idioma_pkey PRIMARY KEY (ref_cod_vps_entrevista, ref_cod_vps_idioma);

--
-- Name: vps_aluno_entrevista_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace:
--

ALTER TABLE ONLY pmieducar.vps_aluno_entrevista
	ADD CONSTRAINT vps_aluno_entrevista_pkey PRIMARY KEY (cod_vps_aluno_entrevista);

--
-- Name: aluno_vps_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace:
--

ALTER TABLE ONLY pmieducar.aluno_vps
	ADD CONSTRAINT aluno_vps_pkey PRIMARY KEY (cod_aluno_vps);

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.vps_jornada_trabalho
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.vps_funcao
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.vps_idioma
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.vps_responsavel_entrevista
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.vps_tipo_contratacao
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.vps_entrevista
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.vps_entrevista_responsavel
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.vps_entrevista_idioma
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.vps_aluno_entrevista
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.aluno_vps
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

--
-- Name: vps_jornada_trabalho_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_jornada_trabalho
	ADD CONSTRAINT vps_jornada_trabalho_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_jornada_trabalho_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_jornada_trabalho
	ADD CONSTRAINT vps_jornada_trabalho_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_jornada_trabalho_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_jornada_trabalho
	ADD CONSTRAINT vps_jornada_trabalho_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_funcao_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_funcao
	ADD CONSTRAINT vps_funcao_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_funcao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_funcao
	ADD CONSTRAINT vps_funcao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_funcao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_funcao
	ADD CONSTRAINT vps_funcao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: vps_idioma_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_idioma
	ADD CONSTRAINT vps_idioma_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_idioma_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_idioma
	ADD CONSTRAINT vps_idioma_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_idioma_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_idioma
	ADD CONSTRAINT vps_idioma_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_responsavel_entrevista_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_responsavel_entrevista
	ADD CONSTRAINT vps_responsavel_entrevista_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_responsavel_entrevista_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_responsavel_entrevista
    ADD CONSTRAINT vps_responsavel_entrevista_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.juridica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_responsavel_entrevista_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_responsavel_entrevista
	ADD CONSTRAINT vps_responsavel_entrevista_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_responsavel_entrevista_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_responsavel_entrevista
	ADD CONSTRAINT vps_responsavel_entrevista_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_tipo_contratacao_ref_cod_instituicao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_tipo_contratacao
	ADD CONSTRAINT vps_tipo_contratacao_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao) REFERENCES pmieducar.instituicao(cod_instituicao) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_tipo_contratacao_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_tipo_contratacao
	ADD CONSTRAINT vps_tipo_contratacao_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_tipo_contratacao_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_tipo_contratacao
	ADD CONSTRAINT vps_tipo_contratacao_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_entrevista_ref_cod_vps_entrevista_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista
	ADD CONSTRAINT vps_entrevista_ref_cod_vps_entrevista_fkey FOREIGN KEY (ref_cod_vps_entrevista) REFERENCES pmieducar.vps_entrevista(cod_vps_entrevista) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_entrevista_ref_cod_escola_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista
	ADD CONSTRAINT vps_entrevista_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola(cod_escola) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_entrevista_ref_cod_curso_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista
	ADD CONSTRAINT vps_entrevista_ref_cod_curso_fkey FOREIGN KEY (ref_cod_curso) REFERENCES pmieducar.curso(cod_curso) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_entrevista_ref_cod_vps_tipo_contratacao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista
	ADD CONSTRAINT vps_entrevista_ref_cod_vps_tipo_contratacao_fkey FOREIGN KEY (ref_cod_vps_tipo_contratacao) REFERENCES pmieducar.vps_tipo_contratacao(cod_vps_tipo_contratacao) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_entrevista_ref_cod_vps_funcao_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista
	ADD CONSTRAINT vps_entrevista_ref_cod_vps_funcao_fkey FOREIGN KEY (ref_cod_vps_funcao) REFERENCES pmieducar.vps_funcao(cod_vps_funcao) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_entrevista_ref_cod_vps_jornada_trabalho_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista
	ADD CONSTRAINT vps_entrevista_ref_cod_vps_jornada_trabalho_fkey FOREIGN KEY (ref_cod_vps_jornada_trabalho) REFERENCES pmieducar.vps_jornada_trabalho(cod_vps_jornada_trabalho) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_entrevista_ref_idpes_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista
	ADD CONSTRAINT vps_entrevista_ref_idpes_fkey FOREIGN KEY (ref_idpes) REFERENCES cadastro.juridica(idpes) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_entrevista_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista
	ADD CONSTRAINT vps_entrevista_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_entrevista_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista
	ADD CONSTRAINT vps_entrevista_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;


--
-- Name: vps_entrevista_responsavel_ref_cod_vps_entrevista_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista_responsavel
	ADD CONSTRAINT vps_entrevista_responsavel_ref_cod_vps_entrevista_fkey FOREIGN KEY (ref_cod_vps_entrevista) REFERENCES pmieducar.vps_entrevista(cod_vps_entrevista) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_entrevista_responsavel_ref_cod_vps_responsavel_entrevista_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista_responsavel
	ADD CONSTRAINT vps_entrevista_responsavel_ref_cod_vps_responsavel_entrevista_fkey FOREIGN KEY (ref_cod_vps_responsavel_entrevista) REFERENCES pmieducar.vps_responsavel_entrevista(cod_vps_responsavel_entrevista) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_entrevista_idioma_ref_cod_vps_entrevista_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista_idioma
	ADD CONSTRAINT vps_entrevista_idioma_ref_cod_vps_entrevista_fkey FOREIGN KEY (ref_cod_vps_entrevista) REFERENCES pmieducar.vps_entrevista(cod_vps_entrevista) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_entrevista_idioma_ref_cod_vps_idioma_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_entrevista_idioma
	ADD CONSTRAINT vps_entrevista_idioma_ref_cod_vps_idioma_fkey FOREIGN KEY (ref_cod_vps_idioma) REFERENCES pmieducar.vps_idioma(cod_vps_idioma) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_aluno_entrevista_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_aluno_entrevista
	ADD CONSTRAINT vps_aluno_entrevista_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_aluno_entrevista_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_aluno_entrevista
	ADD CONSTRAINT vps_aluno_entrevista_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_aluno_entrevista_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_aluno_entrevista
	ADD CONSTRAINT vps_aluno_entrevista_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: vps_aluno_entrevista_ref_cod_vps_entrevista_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.vps_aluno_entrevista
	ADD CONSTRAINT vps_aluno_entrevista_ref_cod_vps_entrevista_fkey FOREIGN KEY (ref_cod_vps_entrevista) REFERENCES pmieducar.vps_entrevista(cod_vps_entrevista) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: aluno_vps_ref_usuario_cad_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.aluno_vps
	ADD CONSTRAINT aluno_vps_ref_usuario_cad_fkey FOREIGN KEY (ref_usuario_cad) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: aluno_vps_ref_usuario_exc_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.aluno_vps
	ADD CONSTRAINT aluno_vps_ref_usuario_exc_fkey FOREIGN KEY (ref_usuario_exc) REFERENCES pmieducar.usuario(cod_usuario) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: aluno_vps_ref_cod_aluno_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.aluno_vps
	ADD CONSTRAINT aluno_vps_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno) REFERENCES pmieducar.aluno(cod_aluno) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: aluno_vps_ref_cod_vps_entrevista_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY pmieducar.aluno_vps
	ADD CONSTRAINT aluno_vps_ref_cod_vps_entrevista_fkey FOREIGN KEY (ref_cod_vps_aluno_entrevista) REFERENCES pmieducar.vps_aluno_entrevista(cod_vps_aluno_entrevista) ON UPDATE RESTRICT ON DELETE RESTRICT;

ALTER SEQUENCE pmieducar.vps_jornada_trabalho_cod_vps_jornada_trabalho_seq
	MINVALUE 0;
SELECT setval('pmieducar.vps_jornada_trabalho_cod_vps_jornada_trabalho_seq', 1, false);

ALTER SEQUENCE pmieducar.vps_funcao_cod_vps_funcao_seq
	MINVALUE 0;
SELECT setval('pmieducar.vps_funcao_cod_vps_funcao_seq', 1, false);

ALTER SEQUENCE pmieducar.vps_idioma_cod_vps_idioma_seq
	MINVALUE 0;
SELECT setval('pmieducar.vps_idioma_cod_vps_idioma_seq', 1, false);

ALTER SEQUENCE pmieducar.vps_responsavel_entrevista_cod_vps_responsavel_entrevista_seq
	MINVALUE 0;
SELECT setval('pmieducar.vps_responsavel_entrevista_cod_vps_responsavel_entrevista_seq', 1, false);

ALTER SEQUENCE pmieducar.vps_tipo_contratacao_cod_vps_tipo_contratacao_seq
	MINVALUE 0;
SELECT setval('pmieducar.vps_tipo_contratacao_cod_vps_tipo_contratacao_seq', 1, false);

ALTER SEQUENCE pmieducar.vps_entrevista_cod_vps_entrevista_seq
	MINVALUE 0;
SELECT setval('pmieducar.vps_entrevista_cod_vps_entrevista_seq', 1, false);

ALTER SEQUENCE pmieducar.vps_aluno_entrevista_cod_vps_aluno_entrevista_seq
	MINVALUE 0;
SELECT setval('pmieducar.vps_aluno_entrevista_cod_vps_aluno_entrevista_seq', 1, false);

ALTER SEQUENCE pmieducar.aluno_vps_cod_aluno_vps_seq
	MINVALUE 0;
SELECT setval('pmieducar.aluno_vps_cod_aluno_vps_seq', 1, false);
