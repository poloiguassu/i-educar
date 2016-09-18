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
-- Name: vps_jornada_trabalho_pkey; Type: CONSTRAINT; Schema: pmieducar; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pmieducar.vps_jornada_trabalho
	ADD CONSTRAINT vps_jornada_trabalho_pkey PRIMARY KEY (cod_vps_jornada_trabalho);

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: pmieducar; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON pmieducar.vps_jornada_trabalho
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


ALTER SEQUENCE pmieducar.vps_jornada_trabalho_cod_vps_jornada_trabalho_seq
	MINVALUE 0;
SELECT setval('pmieducar.vps_jornada_trabalho_cod_vps_jornada_trabalho_seq', 1, false);
