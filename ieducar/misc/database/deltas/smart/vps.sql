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
-- Name: pmieducar.vps_jornada_trabalho; Type: TABLE; Schema: pmieducar; Owner: -; Tablespace: 
--

CREATE TABLE pmieducar.vps_jornada_trabalho (
	cod_vps_jornada_trabalho integer DEFAULT nextval('vps_jornada_trabalho_cod_vps_jornada_trabalho_seq'::regclass) NOT NULL,
	ref_usuario_exc integer,
	ref_usuario_cad integer NOT NULL,
	nm_jornada_trabalho character varying(255) NOT NULL,
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
