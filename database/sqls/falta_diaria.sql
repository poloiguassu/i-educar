--
-- Name: falta_aluno_diaria_cod_falta_aluno_diaria_seq; Type: SEQUENCE; Schema: modules; Owner: -
--

CREATE SEQUENCE modules.falta_aluno_diaria_id_seq
	START WITH 1
	INCREMENT BY 1
	NO MAXVALUE
	MINVALUE 0
	CACHE 1;

--
-- Name: modules.falta_aluno_diaria; Type: TABLE; Schema: modules; Owner: -; Tablespace:
--

CREATE TABLE modules.falta_aluno_diaria (
	id integer DEFAULT nextval('falta_aluno_diaria_id_seq'::regclass) NOT NULL,
	ref_cod_matricula integer NOT NULL,
	ref_cod_quadro_horario_horarios integer NOT NULL,
	situacao smallint NOT NULL
);

--
-- Name: falta_aluno_diaria_pkey; Type: CONSTRAINT; Schema: modules; Owner: -; Tablespace:
--

ALTER TABLE ONLY modules.falta_aluno_diaria
	ADD CONSTRAINT falta_aluno_diaria_pkey PRIMARY KEY (ref_cod_matricula, ref_cod_quadro_horario_horarios);

--
-- Name: falta_aluno_diaria_ref_cod_matricula_fkey; Type: FK CONSTRAINT; Schema: pmieducar; Owner: -
--

ALTER TABLE ONLY modules.falta_aluno_diaria
	ADD CONSTRAINT falta_aluno_diaria_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula) REFERENCES pmieducar.matricula(cod_matricula) ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: falta_aluno_diaria_ref_cod_quadro_horario_horarios_fkey; Type: FK CONSTRAINT; Schema: modules; Owner: -
--

ALTER TABLE ONLY modules.falta_aluno_diaria
	ADD CONSTRAINT falta_aluno_diaria_ref_cod_quadro_horario_horarios_fkey FOREIGN KEY (ref_cod_quadro_horario_horarios) REFERENCES pmieducar.quadro_horario_horarios (cod_quadro_horario_horarios) MATCH SIMPLE ON UPDATE RESTRICT ON DELETE RESTRICT;

--
-- Name: fcn_aft_update; Type: TRIGGER; Schema: modules; Owner: -
--

CREATE TRIGGER fcn_aft_update
	AFTER INSERT OR UPDATE ON modules.falta_aluno_diaria
	FOR EACH ROW
	EXECUTE PROCEDURE fcn_aft_update();

ALTER SEQUENCE modules.falta_aluno_diaria_id_seq
	MINVALUE 0;
SELECT setval('modules.falta_aluno_diaria_id_seq', 1, false);