--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- Name: check_rating; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE check_rating AS ENUM (
    'hidden',
    'info',
    'low_risk',
    'med_risk',
    'high_risk'
);


ALTER TYPE public.check_rating OWNER TO postgres;

--
-- Name: check_status; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE check_status AS ENUM (
    'open',
    'in_progress',
    'stop',
    'finished'
);


ALTER TYPE public.check_status OWNER TO postgres;

--
-- Name: project_status; Type: TYPE; Schema: public; Owner: gtta
--

CREATE TYPE project_status AS ENUM (
    'open',
    'in_progress',
    'finished'
);


ALTER TYPE public.project_status OWNER TO gtta;

--
-- Name: user_role; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE user_role AS ENUM (
    'admin',
    'user',
    'client'
);


ALTER TYPE public.user_role OWNER TO postgres;

--
-- Name: vuln_status; Type: TYPE; Schema: public; Owner: gtta
--

CREATE TYPE vuln_status AS ENUM (
    'open',
    'resolved'
);


ALTER TYPE public.vuln_status OWNER TO gtta;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: check_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_categories (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL
);


ALTER TABLE public.check_categories OWNER TO gtta;

--
-- Name: check_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.check_categories_id_seq OWNER TO gtta;

--
-- Name: check_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_categories_id_seq OWNED BY check_categories.id;


--
-- Name: check_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_categories_id_seq', 11, true);


--
-- Name: check_categories_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_categories_l10n (
    check_category_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.check_categories_l10n OWNER TO gtta;

--
-- Name: check_controls; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_controls (
    id bigint NOT NULL,
    check_category_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.check_controls OWNER TO gtta;

--
-- Name: check_controls_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_controls_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.check_controls_id_seq OWNER TO gtta;

--
-- Name: check_controls_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_controls_id_seq OWNED BY check_controls.id;


--
-- Name: check_controls_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_controls_id_seq', 17, true);


--
-- Name: check_controls_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_controls_l10n (
    check_control_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.check_controls_l10n OWNER TO gtta;

--
-- Name: check_inputs; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_inputs (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    description character varying,
    sort_order integer DEFAULT 0 NOT NULL,
    value character varying,
    type integer DEFAULT 0 NOT NULL,
    check_script_id bigint
);


ALTER TABLE public.check_inputs OWNER TO gtta;

--
-- Name: check_inputs_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_inputs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.check_inputs_id_seq OWNER TO gtta;

--
-- Name: check_inputs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_inputs_id_seq OWNED BY check_inputs.id;


--
-- Name: check_inputs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_inputs_id_seq', 68, true);


--
-- Name: check_inputs_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_inputs_l10n (
    check_input_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000),
    description character varying
);


ALTER TABLE public.check_inputs_l10n OWNER TO gtta;

--
-- Name: check_results; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_results (
    id bigint NOT NULL,
    check_id bigint NOT NULL,
    result character varying NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    title character varying(1000) NOT NULL
);


ALTER TABLE public.check_results OWNER TO gtta;

--
-- Name: check_results_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_results_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.check_results_id_seq OWNER TO gtta;

--
-- Name: check_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_results_id_seq OWNED BY check_results.id;


--
-- Name: check_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_results_id_seq', 6, true);


--
-- Name: check_results_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_results_l10n (
    check_result_id bigint NOT NULL,
    language_id bigint NOT NULL,
    result character varying,
    title character varying(1000)
);


ALTER TABLE public.check_results_l10n OWNER TO gtta;

--
-- Name: check_scripts; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_scripts (
    id bigint NOT NULL,
    check_id bigint NOT NULL,
    name character varying(1000) NOT NULL
);


ALTER TABLE public.check_scripts OWNER TO gtta;

--
-- Name: check_scripts_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_scripts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.check_scripts_id_seq OWNER TO gtta;

--
-- Name: check_scripts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_scripts_id_seq OWNED BY check_scripts.id;


--
-- Name: check_scripts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_scripts_id_seq', 48, true);


--
-- Name: check_solutions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_solutions (
    id bigint NOT NULL,
    check_id bigint NOT NULL,
    solution character varying NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    title character varying(1000) NOT NULL
);


ALTER TABLE public.check_solutions OWNER TO gtta;

--
-- Name: check_solutions_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_solutions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.check_solutions_id_seq OWNER TO gtta;

--
-- Name: check_solutions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_solutions_id_seq OWNED BY check_solutions.id;


--
-- Name: check_solutions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_solutions_id_seq', 7, true);


--
-- Name: check_solutions_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_solutions_l10n (
    check_solution_id bigint NOT NULL,
    language_id bigint NOT NULL,
    solution character varying,
    title character varying(1000)
);


ALTER TABLE public.check_solutions_l10n OWNER TO gtta;

--
-- Name: checks; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE checks (
    id bigint NOT NULL,
    check_control_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    background_info character varying,
    hints character varying,
    advanced boolean NOT NULL,
    automated boolean NOT NULL,
    multiple_solutions boolean NOT NULL,
    protocol character varying(1000),
    port integer,
    question character varying,
    reference_id bigint NOT NULL,
    reference_code character varying(1000),
    reference_url character varying(1000),
    effort integer DEFAULT 0 NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.checks OWNER TO gtta;

--
-- Name: checks_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE checks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.checks_id_seq OWNER TO gtta;

--
-- Name: checks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE checks_id_seq OWNED BY checks.id;


--
-- Name: checks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('checks_id_seq', 56, true);


--
-- Name: checks_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE checks_l10n (
    check_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000),
    background_info character varying,
    hints character varying,
    reference character varying,
    question character varying
);


ALTER TABLE public.checks_l10n OWNER TO gtta;

--
-- Name: clients; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE clients (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    country character varying(1000),
    state character varying(1000),
    city character varying(1000),
    address character varying(1000),
    postcode character varying(1000),
    website character varying(1000),
    contact_name character varying(1000),
    contact_phone character varying(1000),
    contact_email character varying(1000),
    contact_fax character varying(1000),
    logo_path character varying(1000),
    logo_type character varying(1000)
);


ALTER TABLE public.clients OWNER TO gtta;

--
-- Name: clients_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.clients_id_seq OWNER TO gtta;

--
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE clients_id_seq OWNED BY clients.id;


--
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('clients_id_seq', 4, true);


--
-- Name: emails; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE emails (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    subject character varying(1000) NOT NULL,
    content character varying NOT NULL,
    attempts integer DEFAULT 0 NOT NULL,
    sent boolean DEFAULT false NOT NULL
);


ALTER TABLE public.emails OWNER TO gtta;

--
-- Name: emails_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE emails_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.emails_id_seq OWNER TO gtta;

--
-- Name: emails_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE emails_id_seq OWNED BY emails.id;


--
-- Name: emails_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('emails_id_seq', 18, true);


--
-- Name: gt_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE gt_categories (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL
);


ALTER TABLE public.gt_categories OWNER TO gtta;

--
-- Name: gt_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE gt_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.gt_categories_id_seq OWNER TO gtta;

--
-- Name: gt_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE gt_categories_id_seq OWNED BY gt_categories.id;


--
-- Name: gt_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('gt_categories_id_seq', 2, true);


--
-- Name: gt_categories_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE gt_categories_l10n (
    gt_category_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.gt_categories_l10n OWNER TO gtta;

--
-- Name: gt_check_dependencies; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE gt_check_dependencies (
    id bigint NOT NULL,
    gt_check_id bigint NOT NULL,
    gt_module_id bigint NOT NULL,
    condition character varying(1000) NOT NULL
);


ALTER TABLE public.gt_check_dependencies OWNER TO gtta;

--
-- Name: gt_check_dependencies_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE gt_check_dependencies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.gt_check_dependencies_id_seq OWNER TO gtta;

--
-- Name: gt_check_dependencies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE gt_check_dependencies_id_seq OWNED BY gt_check_dependencies.id;


--
-- Name: gt_check_dependencies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('gt_check_dependencies_id_seq', 3, true);


--
-- Name: gt_checks; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE gt_checks (
    id bigint NOT NULL,
    gt_module_id bigint NOT NULL,
    check_id bigint NOT NULL,
    description character varying,
    target_description character varying(1000),
    sort_order integer DEFAULT 0 NOT NULL,
    gt_dependency_processor_id bigint
);


ALTER TABLE public.gt_checks OWNER TO gtta;

--
-- Name: gt_checks_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE gt_checks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.gt_checks_id_seq OWNER TO gtta;

--
-- Name: gt_checks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE gt_checks_id_seq OWNED BY gt_checks.id;


--
-- Name: gt_checks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('gt_checks_id_seq', 9, true);


--
-- Name: gt_checks_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE gt_checks_l10n (
    gt_check_id bigint NOT NULL,
    language_id bigint NOT NULL,
    description character varying,
    target_description character varying
);


ALTER TABLE public.gt_checks_l10n OWNER TO gtta;

--
-- Name: gt_dependency_processors; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE gt_dependency_processors (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL
);


ALTER TABLE public.gt_dependency_processors OWNER TO gtta;

--
-- Name: gt_dependency_processors_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE gt_dependency_processors_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.gt_dependency_processors_id_seq OWNER TO gtta;

--
-- Name: gt_dependency_processors_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE gt_dependency_processors_id_seq OWNED BY gt_dependency_processors.id;


--
-- Name: gt_dependency_processors_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('gt_dependency_processors_id_seq', 1, true);


--
-- Name: gt_modules; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE gt_modules (
    id bigint NOT NULL,
    gt_type_id bigint NOT NULL,
    name character varying(1000) NOT NULL
);


ALTER TABLE public.gt_modules OWNER TO gtta;

--
-- Name: gt_modules_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE gt_modules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.gt_modules_id_seq OWNER TO gtta;

--
-- Name: gt_modules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE gt_modules_id_seq OWNED BY gt_modules.id;


--
-- Name: gt_modules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('gt_modules_id_seq', 5, true);


--
-- Name: gt_modules_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE gt_modules_l10n (
    gt_module_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.gt_modules_l10n OWNER TO gtta;

--
-- Name: gt_types; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE gt_types (
    id bigint NOT NULL,
    gt_category_id bigint NOT NULL,
    name character varying(1000) NOT NULL
);


ALTER TABLE public.gt_types OWNER TO gtta;

--
-- Name: gt_types_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE gt_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.gt_types_id_seq OWNER TO gtta;

--
-- Name: gt_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE gt_types_id_seq OWNED BY gt_types.id;


--
-- Name: gt_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('gt_types_id_seq', 8, true);


--
-- Name: gt_types_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE gt_types_l10n (
    gt_type_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.gt_types_l10n OWNER TO gtta;

--
-- Name: languages; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE languages (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    code character(2) NOT NULL,
    "default" boolean NOT NULL
);


ALTER TABLE public.languages OWNER TO gtta;

--
-- Name: languages_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE languages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.languages_id_seq OWNER TO gtta;

--
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE languages_id_seq OWNED BY languages.id;


--
-- Name: languages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('languages_id_seq', 3, true);


--
-- Name: login_history; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE login_history (
    id bigint NOT NULL,
    user_id bigint,
    user_name character varying(1000) NOT NULL,
    create_time timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.login_history OWNER TO gtta;

--
-- Name: login_history_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE login_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.login_history_id_seq OWNER TO gtta;

--
-- Name: login_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE login_history_id_seq OWNED BY login_history.id;


--
-- Name: login_history_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('login_history_id_seq', 130, true);


--
-- Name: project_details; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_details (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    subject character varying(1000) NOT NULL,
    content character varying
);


ALTER TABLE public.project_details OWNER TO gtta;

--
-- Name: project_details_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE project_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.project_details_id_seq OWNER TO gtta;

--
-- Name: project_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE project_details_id_seq OWNED BY project_details.id;


--
-- Name: project_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('project_details_id_seq', 4, true);


--
-- Name: project_gt_check_attachments; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_gt_check_attachments (
    project_id bigint NOT NULL,
    gt_check_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    type character varying(1000) NOT NULL,
    path character varying(1000) NOT NULL,
    size bigint DEFAULT 0 NOT NULL
);


ALTER TABLE public.project_gt_check_attachments OWNER TO gtta;

--
-- Name: project_gt_check_inputs; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_gt_check_inputs (
    project_id bigint NOT NULL,
    gt_check_id bigint NOT NULL,
    check_input_id bigint NOT NULL,
    value character varying,
    file character varying(1000)
);


ALTER TABLE public.project_gt_check_inputs OWNER TO gtta;

--
-- Name: project_gt_check_solutions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_gt_check_solutions (
    project_id bigint NOT NULL,
    gt_check_id bigint NOT NULL,
    check_solution_id bigint NOT NULL
);


ALTER TABLE public.project_gt_check_solutions OWNER TO gtta;

--
-- Name: project_gt_checks; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_gt_checks (
    project_id bigint NOT NULL,
    gt_check_id bigint NOT NULL,
    user_id bigint NOT NULL,
    language_id bigint NOT NULL,
    target character varying(1000),
    port integer,
    protocol character varying(1000),
    target_file character varying(1000),
    result_file character varying(1000),
    result character varying,
    table_result character varying,
    started timestamp without time zone,
    pid bigint,
    rating check_rating,
    status check_status
);


ALTER TABLE public.project_gt_checks OWNER TO gtta;

--
-- Name: project_gt_modules; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_gt_modules (
    project_id bigint NOT NULL,
    gt_module_id bigint NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.project_gt_modules OWNER TO gtta;

--
-- Name: project_gt_suggested_targets; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_gt_suggested_targets (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    gt_module_id bigint NOT NULL,
    target character varying(1000) NOT NULL,
    gt_check_id bigint NOT NULL,
    approved boolean DEFAULT false NOT NULL
);


ALTER TABLE public.project_gt_suggested_targets OWNER TO gtta;

--
-- Name: project_gt_suggested_targets_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE project_gt_suggested_targets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.project_gt_suggested_targets_id_seq OWNER TO gtta;

--
-- Name: project_gt_suggested_targets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE project_gt_suggested_targets_id_seq OWNED BY project_gt_suggested_targets.id;


--
-- Name: project_gt_suggested_targets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('project_gt_suggested_targets_id_seq', 15, true);


--
-- Name: project_users; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_users (
    project_id bigint NOT NULL,
    user_id bigint NOT NULL,
    admin boolean DEFAULT false NOT NULL
);


ALTER TABLE public.project_users OWNER TO gtta;

--
-- Name: projects; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE projects (
    id bigint NOT NULL,
    client_id bigint NOT NULL,
    year character(4) NOT NULL,
    deadline date NOT NULL,
    name character varying(1000) NOT NULL,
    status project_status DEFAULT 'open'::project_status NOT NULL,
    vuln_overdue date,
    guided_test boolean DEFAULT false NOT NULL
);


ALTER TABLE public.projects OWNER TO gtta;

--
-- Name: projects_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE projects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.projects_id_seq OWNER TO gtta;

--
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE projects_id_seq OWNED BY projects.id;


--
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('projects_id_seq', 13, true);


--
-- Name: references; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE "references" (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    url character varying(1000)
);


ALTER TABLE public."references" OWNER TO gtta;

--
-- Name: references_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE references_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.references_id_seq OWNER TO gtta;

--
-- Name: references_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE references_id_seq OWNED BY "references".id;


--
-- Name: references_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('references_id_seq', 2, true);


--
-- Name: report_template_sections; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_template_sections (
    id bigint NOT NULL,
    report_template_id bigint NOT NULL,
    check_category_id bigint NOT NULL,
    intro character varying,
    sort_order integer DEFAULT 0 NOT NULL,
    title character varying(1000)
);


ALTER TABLE public.report_template_sections OWNER TO gtta;

--
-- Name: report_template_sections_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE report_template_sections_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.report_template_sections_id_seq OWNER TO gtta;

--
-- Name: report_template_sections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE report_template_sections_id_seq OWNED BY report_template_sections.id;


--
-- Name: report_template_sections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('report_template_sections_id_seq', 4, true);


--
-- Name: report_template_sections_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_template_sections_l10n (
    report_template_section_id bigint NOT NULL,
    language_id bigint NOT NULL,
    intro character varying,
    title character varying(1000)
);


ALTER TABLE public.report_template_sections_l10n OWNER TO gtta;

--
-- Name: report_template_summary; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_template_summary (
    id bigint NOT NULL,
    summary character varying,
    rating_from numeric(3,2) DEFAULT 0.00 NOT NULL,
    rating_to numeric(3,2) DEFAULT 0.00 NOT NULL,
    report_template_id bigint NOT NULL,
    title character varying(1000)
);


ALTER TABLE public.report_template_summary OWNER TO gtta;

--
-- Name: report_template_summary_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE report_template_summary_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.report_template_summary_id_seq OWNER TO gtta;

--
-- Name: report_template_summary_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE report_template_summary_id_seq OWNED BY report_template_summary.id;


--
-- Name: report_template_summary_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('report_template_summary_id_seq', 4, true);


--
-- Name: report_template_summary_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_template_summary_l10n (
    report_template_summary_id bigint NOT NULL,
    language_id bigint NOT NULL,
    summary character varying,
    title character varying(1000)
);


ALTER TABLE public.report_template_summary_l10n OWNER TO gtta;

--
-- Name: report_templates; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_templates (
    id bigint NOT NULL,
    name character varying(1000),
    header_image_path character varying(1000),
    header_image_type character varying(1000),
    intro character varying,
    appendix character varying,
    vulns_intro character varying,
    info_checks_intro character varying,
    security_level_intro character varying,
    vuln_distribution_intro character varying,
    reduced_intro character varying,
    high_description character varying,
    med_description character varying,
    low_description character varying,
    degree_intro character varying,
    risk_intro character varying,
    footer character varying
);


ALTER TABLE public.report_templates OWNER TO gtta;

--
-- Name: report_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE report_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.report_templates_id_seq OWNER TO gtta;

--
-- Name: report_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE report_templates_id_seq OWNED BY report_templates.id;


--
-- Name: report_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('report_templates_id_seq', 3, true);


--
-- Name: report_templates_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE report_templates_l10n (
    report_template_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000),
    intro character varying,
    appendix character varying,
    vulns_intro character varying,
    info_checks_intro character varying,
    security_level_intro character varying,
    vuln_distribution_intro character varying,
    reduced_intro character varying,
    high_description character varying,
    med_description character varying,
    low_description character varying,
    degree_intro character varying,
    risk_intro character varying,
    footer character varying
);


ALTER TABLE public.report_templates_l10n OWNER TO gtta;

--
-- Name: risk_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_categories (
    id bigint NOT NULL,
    name character varying(1000),
    risk_template_id bigint NOT NULL
);


ALTER TABLE public.risk_categories OWNER TO gtta;

--
-- Name: risk_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE risk_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.risk_categories_id_seq OWNER TO gtta;

--
-- Name: risk_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE risk_categories_id_seq OWNED BY risk_categories.id;


--
-- Name: risk_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('risk_categories_id_seq', 20, true);


--
-- Name: risk_categories_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_categories_l10n (
    risk_category_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_categories_l10n OWNER TO gtta;

--
-- Name: risk_category_checks; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_category_checks (
    risk_category_id bigint NOT NULL,
    check_id bigint NOT NULL,
    damage integer DEFAULT 0 NOT NULL,
    likelihood integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.risk_category_checks OWNER TO gtta;

--
-- Name: risk_templates; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_templates (
    id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_templates OWNER TO gtta;

--
-- Name: risk_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE risk_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.risk_templates_id_seq OWNER TO gtta;

--
-- Name: risk_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE risk_templates_id_seq OWNED BY risk_templates.id;


--
-- Name: risk_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('risk_templates_id_seq', 6, true);


--
-- Name: risk_templates_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_templates_l10n (
    risk_template_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_templates_l10n OWNER TO gtta;

--
-- Name: sessions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE sessions (
    id character(32) NOT NULL,
    expire integer,
    data text
);


ALTER TABLE public.sessions OWNER TO gtta;

--
-- Name: system; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE system (
    id bigint NOT NULL,
    backup timestamp without time zone,
    timezone character varying(1000)
);


ALTER TABLE public.system OWNER TO gtta;

--
-- Name: system_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE system_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.system_id_seq OWNER TO gtta;

--
-- Name: system_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE system_id_seq OWNED BY system.id;


--
-- Name: system_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('system_id_seq', 2, true);


--
-- Name: target_check_attachments; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_attachments (
    target_id bigint NOT NULL,
    check_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    type character varying(1000) NOT NULL,
    path character varying(1000) NOT NULL,
    size bigint DEFAULT 0 NOT NULL
);


ALTER TABLE public.target_check_attachments OWNER TO gtta;

--
-- Name: target_check_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_categories (
    target_id bigint NOT NULL,
    check_category_id bigint NOT NULL,
    advanced boolean NOT NULL,
    check_count bigint DEFAULT 0 NOT NULL,
    finished_count bigint DEFAULT 0 NOT NULL,
    low_risk_count bigint DEFAULT 0 NOT NULL,
    med_risk_count bigint DEFAULT 0 NOT NULL,
    high_risk_count bigint DEFAULT 0 NOT NULL,
    info_count bigint DEFAULT 0 NOT NULL
);


ALTER TABLE public.target_check_categories OWNER TO gtta;

--
-- Name: target_check_inputs; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_inputs (
    target_id bigint NOT NULL,
    check_input_id bigint NOT NULL,
    value character varying,
    file character varying(1000),
    check_id bigint NOT NULL
);


ALTER TABLE public.target_check_inputs OWNER TO gtta;

--
-- Name: target_check_solutions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_solutions (
    target_id bigint NOT NULL,
    check_solution_id bigint NOT NULL,
    check_id bigint NOT NULL
);


ALTER TABLE public.target_check_solutions OWNER TO gtta;

--
-- Name: target_check_vulns; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_vulns (
    target_id bigint NOT NULL,
    check_id bigint NOT NULL,
    user_id bigint,
    deadline date,
    status vuln_status DEFAULT 'open'::vuln_status NOT NULL
);


ALTER TABLE public.target_check_vulns OWNER TO gtta;

--
-- Name: target_checks; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_checks (
    target_id bigint NOT NULL,
    check_id bigint NOT NULL,
    result character varying,
    target_file character varying(1000),
    rating check_rating,
    started timestamp without time zone,
    pid bigint,
    status check_status DEFAULT 'open'::check_status NOT NULL,
    result_file character varying(1000),
    language_id bigint NOT NULL,
    protocol character varying(1000),
    port integer,
    override_target character varying(1000),
    user_id bigint NOT NULL,
    table_result character varying
);


ALTER TABLE public.target_checks OWNER TO gtta;

--
-- Name: target_references; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_references (
    target_id bigint NOT NULL,
    reference_id bigint NOT NULL
);


ALTER TABLE public.target_references OWNER TO gtta;

--
-- Name: targets; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE targets (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    host character varying(1000) NOT NULL,
    description character varying(1000)
);


ALTER TABLE public.targets OWNER TO gtta;

--
-- Name: targets_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE targets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.targets_id_seq OWNER TO gtta;

--
-- Name: targets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE targets_id_seq OWNED BY targets.id;


--
-- Name: targets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('targets_id_seq', 7, true);


--
-- Name: users; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE users (
    id bigint NOT NULL,
    email character varying(1000) NOT NULL,
    password character varying(1000) NOT NULL,
    name character varying(1000),
    client_id bigint,
    role user_role DEFAULT 'admin'::user_role NOT NULL,
    last_action_time timestamp without time zone,
    send_notifications boolean DEFAULT false NOT NULL,
    password_reset_code character varying(1000),
    password_reset_time timestamp(6) without time zone,
    show_reports boolean DEFAULT false NOT NULL,
    show_details boolean DEFAULT false NOT NULL,
    certificate_required boolean DEFAULT false NOT NULL,
    certificate_serial character varying(1000),
    certificate_issuer character varying(1000)
);


ALTER TABLE public.users OWNER TO gtta;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO gtta;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('users_id_seq', 4, true);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories ALTER COLUMN id SET DEFAULT nextval('check_categories_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls ALTER COLUMN id SET DEFAULT nextval('check_controls_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs ALTER COLUMN id SET DEFAULT nextval('check_inputs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results ALTER COLUMN id SET DEFAULT nextval('check_results_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_scripts ALTER COLUMN id SET DEFAULT nextval('check_scripts_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions ALTER COLUMN id SET DEFAULT nextval('check_solutions_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks ALTER COLUMN id SET DEFAULT nextval('checks_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY clients ALTER COLUMN id SET DEFAULT nextval('clients_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails ALTER COLUMN id SET DEFAULT nextval('emails_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_categories ALTER COLUMN id SET DEFAULT nextval('gt_categories_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_check_dependencies ALTER COLUMN id SET DEFAULT nextval('gt_check_dependencies_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_checks ALTER COLUMN id SET DEFAULT nextval('gt_checks_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_dependency_processors ALTER COLUMN id SET DEFAULT nextval('gt_dependency_processors_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_modules ALTER COLUMN id SET DEFAULT nextval('gt_modules_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_types ALTER COLUMN id SET DEFAULT nextval('gt_types_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY languages ALTER COLUMN id SET DEFAULT nextval('languages_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY login_history ALTER COLUMN id SET DEFAULT nextval('login_history_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details ALTER COLUMN id SET DEFAULT nextval('project_details_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_suggested_targets ALTER COLUMN id SET DEFAULT nextval('project_gt_suggested_targets_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects ALTER COLUMN id SET DEFAULT nextval('projects_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY "references" ALTER COLUMN id SET DEFAULT nextval('references_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections ALTER COLUMN id SET DEFAULT nextval('report_template_sections_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary ALTER COLUMN id SET DEFAULT nextval('report_template_summary_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_templates ALTER COLUMN id SET DEFAULT nextval('report_templates_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories ALTER COLUMN id SET DEFAULT nextval('risk_categories_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_templates ALTER COLUMN id SET DEFAULT nextval('risk_templates_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY system ALTER COLUMN id SET DEFAULT nextval('system_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets ALTER COLUMN id SET DEFAULT nextval('targets_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Data for Name: check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_categories (id, name) FROM stdin;
2	FTP
3	SMTP
4	SSH
5	TCP
6	Web Anonymous
1	DNS
8	Eine Kleine
10	zed
9	AUTHENTICATED WEB CHECKS
11	New & Modified Checks
\.


--
-- Data for Name: check_categories_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_categories_l10n (check_category_id, language_id, name) FROM stdin;
2	1	FTP
2	2	\N
3	1	SMTP
3	2	\N
4	1	SSH
4	2	\N
5	1	TCP
5	2	\N
6	1	Web Anonymous
6	2	\N
1	1	DNS
1	2	\N
8	1	\N
8	2	Eine Kleine
10	1	zed
10	2	\N
9	1	AUTHENTICATED WEB CHECKS
9	2	Piuyyy
11	1	New & Modified Checks
11	2	\N
\.


--
-- Data for Name: check_controls; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_controls (id, check_category_id, name, sort_order) FROM stdin;
2	2	Default	2
3	3	Default	3
4	4	Default	4
5	5	Default	5
6	6	Default	6
7	1	This is a long name of the control	7
9	1	Some other important stuff	8
11	1	Empty Control	9
8	1	Session Handling	13
10	9	SESSION HANDLING & COOKIES	10
1	1	Default	1
12	11	New checks	12
13	1	1	11
14	1	2	14
15	1	3	15
16	1	4	16
17	1	5	17
\.


--
-- Data for Name: check_controls_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_controls_l10n (check_control_id, language_id, name) FROM stdin;
1	1	Default
1	2	zzz
2	1	Default
2	2	\N
3	1	Default
3	2	\N
4	1	Default
4	2	\N
5	1	Default
5	2	\N
6	1	Default
6	2	\N
7	1	This is a long name of the control
7	2	\N
9	1	Some other important stuff
9	2	\N
11	1	Empty Control
11	2	\N
8	1	Session Handling
8	2	\N
10	1	SESSION HANDLING & COOKIES
10	2	\N
12	1	New checks
12	2	\N
13	1	1
13	2	\N
14	1	2
14	2	\N
15	1	3
15	2	\N
16	1	4
16	2	\N
17	1	5
17	2	\N
\.


--
-- Data for Name: check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs (id, name, description, sort_order, value, type, check_script_id) FROM stdin;
5	Timeout		0	120	0	35
8	Timeout		0	10	0	36
7	Show All		0	0	0	43
11	Hostname		0		0	7
9	Max Results		1	100	0	36
10	Mode	Operation mode: 0 - output generated list only, 1 - resolve IP check	2	1	0	36
14	Long List		0	0	0	9
12	Hostname		0		0	6
13	Long List		0	0	0	12
47	Adobe xml		1		4	32
15	Users		0		0	1
16	Passwords		1		0	1
17	Recipient		0		0	4
18	Server		1		0	4
19	Login		2		0	4
20	Password		3		0	4
21	Sender		4		0	4
22	Folder		5		0	4
23	Timeout		0	10	0	5
24	Source E-mail		1	source@gmail.com	0	5
25	Destination E-mail		2	destination@gmail.com	0	5
26	Users		0		0	13
27	Passwords		1		0	13
28	Port Range	Port range that will be passed to nmap. Please use nmap syntax for -p command line argument (for example, 22; 1-65535; U:53,111,137,T:21-25,80,139,8080)	0		0	14
30	Port Range	2 lines: start and end of the range.	0	1\r\n80	0	15
31	Range Count		0	10	0	17
32	Code	Possible values: php, cfm, asp.	0	php	0	23
34	Paths		0		0	26
33	URLs		0		0	25
35	Paths		0		0	27
36	Paths		0		0	28
37	Timeout		0	10	0	33
1	Hostname X		0	asdfasdf	0	42
6	Debug		1	1	0	35
46	Admin Logins		0		4	32
38	Page Type	Possible values: php, asp.	0	php	0	34
39	Cookies		1		0	34
40	URL Limit		2	100	0	34
42	Hostname		0		0	41
61	Hostname		0		0	45
62	Hostname X		0	asdfasdf	0	46
63	Hostname		0		0	47
64	Show All		0	0	0	48
29	Skip Discovery		1	1	2	14
48	Test File		0		4	44
49	Second File		1		4	44
50	Cool stuff		2		4	44
51	Gangnam		3		4	44
52	Yay		4		4	44
53	Yaka Waka	123123	5	sdfsdf	0	44
54	asdfasdf		6	1	2	44
55	Kuka		7	asdfasdf	2	44
56	Suka		8		2	44
57	Shmaka		9	124234	2	44
58	Kaka		10	kkj	2	44
65	Verbose		2	1	2	14
66	Probe		3		2	14
67	Timing		4	2	0	14
68	Extract		5	1	2	14
\.


--
-- Data for Name: check_inputs_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs_l10n (check_input_id, language_id, name, description) FROM stdin;
1	1	Hostname X	\N
1	2	\N	\N
5	1	Timeout	\N
5	2	\N	\N
6	1	Debug	\N
6	2	\N	\N
7	1	Show All	\N
7	2	\N	\N
8	1	Timeout	\N
8	2	\N	\N
9	1	Max Results	\N
9	2	\N	\N
10	1	Mode	Operation mode: 0 - output generated list only, 1 - resolve IP check
10	2	\N	\N
11	1	Hostname	\N
11	2	\N	\N
12	1	Hostname	\N
12	2	\N	\N
13	1	Long List	\N
13	2	\N	\N
14	1	Long List	\N
14	2	\N	\N
15	1	Users	\N
15	2	\N	\N
16	1	Passwords	\N
16	2	\N	\N
17	1	Recipient	\N
17	2	\N	\N
18	1	Server	\N
18	2	\N	\N
19	1	Login	\N
19	2	\N	\N
20	1	Password	\N
20	2	\N	\N
21	1	Sender	\N
21	2	\N	\N
22	1	Folder	\N
22	2	\N	\N
23	1	Timeout	\N
23	2	\N	\N
24	1	Source E-mail	\N
24	2	\N	\N
25	1	Destination E-mail	\N
25	2	\N	\N
26	1	Users	\N
26	2	\N	\N
27	1	Passwords	\N
27	2	\N	\N
28	1	Port Range	Port range that will be passed to nmap. Please use nmap syntax for -p command line argument (for example, 22; 1-65535; U:53,111,137,T:21-25,80,139,8080)
28	2	\N	\N
30	1	Port Range	2 lines: start and end of the range.
30	2	\N	\N
31	1	Range Count	\N
31	2	\N	\N
32	1	Code	Possible values: php, cfm, asp.
32	2	\N	\N
34	1	Paths	\N
34	2	\N	\N
33	1	URLs	\N
33	2	\N	\N
35	1	Paths	\N
35	2	\N	\N
36	1	Paths	\N
36	2	\N	\N
37	1	Timeout	\N
37	2	\N	\N
38	1	Page Type	Possible values: php, asp.
38	2	\N	\N
39	1	Cookie	\N
39	2	\N	\N
40	1	URL Limit	\N
40	2	\N	\N
42	1	Hostname	\N
42	2	\N	\N
29	1	Skip Discovery	\N
29	2	\N	\N
65	1	Verbose	\N
65	2	\N	\N
66	1	Probe	\N
66	2	\N	\N
67	1	Timing	\N
67	2	\N	\N
68	1	Extract	\N
68	2	\N	\N
61	1	Hostname	\N
61	2	\N	\N
62	1	Hostname X	\N
62	2	\N	\N
46	1	Admin Logins	\N
46	2	\N	\N
63	1	Hostname	\N
63	2	\N	\N
47	1	Adobe xml	\N
47	2	\N	\N
48	1	Test File	\N
48	2	\N	\N
49	1	Second File	\N
49	2	\N	\N
50	1	Cool stuff	\N
50	2	\N	\N
51	1	Gangnam	\N
51	2	\N	\N
52	1	Yay	\N
52	2	\N	\N
53	1	Yaka Waka	123123
53	2	\N	\N
54	1	asdfasdf	\N
54	2	\N	\N
55	1	Kuka	\N
55	2	\N	\N
56	1	Suka	\N
56	2	\N	\N
57	1	Shmaka	\N
57	2	\N	\N
58	1	Kaka	\N
58	2	\N	\N
64	1	Show All	\N
64	2	\N	\N
\.


--
-- Data for Name: check_results; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results (id, check_id, result, sort_order, title) FROM stdin;
3	3	Resulten	1	Test Deutsche
2	3	Here is no formatting at all - because this field is plain text. Please humble with that.\r\n\r\nLine span.	0	Test English
5	46	zzz & xxx <a> lolo	0	xxx
6	55	Test	0	Testx
\.


--
-- Data for Name: check_results_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results_l10n (check_result_id, language_id, result, title) FROM stdin;
3	1	\N	\N
3	2	Resulten	Test Deutsche
2	1	Here is no formatting at all - because this field is plain text. Please humble with that.\r\n\r\nLine span.	Test English
2	2	Result ' Pizda Dzhigurda (de)	Zuzuz
5	1	zzz & xxx <a> lolo	xxx
5	2	\N	\N
6	1	Test	Testx
6	2	\N	\N
\.


--
-- Data for Name: check_scripts; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_scripts (id, check_id, name) FROM stdin;
1	17	ftp_bruteforce.pl
2	18	smtp_banner.py
3	19	smtp_dnsbl.py
4	20	smtp_filter.py
5	21	smtp_relay.pl
6	13	dns_spf.py
7	12	dns_soa.py
8	10	ns_version.pl
9	16	dns_top_tlds.pl
10	9	nic_whois.pl
11	7	dns_ip_range.pl
12	15	subdomain_bruteforce.pl
13	22	ssh_bruteforce.pl
15	24	portscan.pl
16	25	tcp_traceroute.py
17	26	apache_dos.pl
18	27	fuzz_check.pl
19	28	google_url.pl
20	29	grep_url.pl
21	30	http_banner.pl
22	31	joomla_scan.pl
23	32	login_pages.pl
24	33	nikto.pl
25	34	urlscan.pl
26	35	www_auth_scanner.pl
27	36	www_dir_scanner.pl
28	37	www_file_scanner.pl
29	38	web_http_methods.py
30	39	webserver_cms.pl
31	40	webserver_error_msg.pl
32	41	webserver_files.pl
33	42	webserver_ssl.pl
34	43	web_sql_xss.py
35	5	dns_find_ns.pl
36	8	nic_typosquatting.pl
37	11	dns_resolve_ip.pl
38	14	dns_spf.pl
39	47	cms_detection.py
40	3	dns_afxr.pl
41	45	dns_a_nr.py
42	1	dns_a.py
43	6	dns_hosting.py
44	46	test.py
14	23	nmap_tcp.pl
45	1	dns_a_nr.py
46	55	dns_a.py
47	55	dns_a_nr.py
48	56	dns_hosting.py
\.


--
-- Data for Name: check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions (id, check_id, solution, sort_order, title) FROM stdin;
5	3	i love you too	1	tears of prophecy
4	3	<i>zoo</i><br><i>gooooo<br><br></i>pom pom<br><i><br></i><b>black</b>	0	aduljadei
7	46	zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz<br><br><ol><li>asdfsadf</li><li>asdfasdf</li></ol><br>sdfsadf<br><ul><li>dsf</li><li>asdfasdf</li></ul><br>	0	Fuck something
\.


--
-- Data for Name: check_solutions_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions_l10n (check_solution_id, language_id, solution, title) FROM stdin;
5	1	i love you too	tears of prophecy
5	2	ich liebe dir	\N
4	1	<i>zoo</i><br><i>gooooo<br><br></i>pom pom<br><i><br></i><b>black</b>	aduljadei
4	2	\N	\N
7	1	zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz<br><br><ol><li>asdfsadf</li><li>asdfasdf</li></ol><br>sdfsadf<br><ul><li>dsf</li><li>asdfasdf</li></ul><br>	Fuck something
7	2	\N	\N
\.


--
-- Data for Name: checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks (id, check_control_id, name, background_info, hints, advanced, automated, multiple_solutions, protocol, port, question, reference_id, reference_code, reference_url, effort, sort_order) FROM stdin;
7	1	DNS IP Range			f	t	f		\N		1			2	10
9	1	DNS NIC Whois			f	t	f		\N		1			2	12
10	1	DNS NS Version			f	t	f		\N		1			2	13
12	1	DNS SOA			f	t	f		\N		1			2	15
13	1	DNS SPF			f	t	f		\N		1			2	16
15	1	DNS Subdomain Bruteforce			f	t	f		\N		1			2	6
16	1	DNS Top TLDs			f	t	f		\N		1			2	45
17	2	FTP Bruteforce			f	t	f		\N		1			2	17
18	3	SMTP Banner			f	t	f		\N		1			2	18
19	3	SMTP DNSBL			f	t	f		\N		1			2	19
20	3	SMTP Filter			f	t	f		\N		1			2	20
21	3	SMTP Relay			f	t	f		\N		1			2	21
54	1	DNS A (Copy)	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.		f	t	f		\N		1			2	54
46	10	Scan Somethingh	<b>Background Info:</b><br>The http-server headers often leak information about servertype and versions. In our check we request the server via http 1.0 with and without host-header to check for misconfigured vhosts and via a usual http 1.1-request. Having the servertype and version an attacker can start investigating if there are known exploits for that specific version. This helps the attacker to prepare all targeted attacks. We will look specifically for the following tags:<br><br><ul><li>Server: A name for the server including sometimes the version.</li><li>Via: Informs the client of proxies through which the response was sent.</li><li>X-Powered-By: specifies the technology (e.g. ASP.NET, PHP, JBoss) supporting the web application (version details are often in X-Runtime, X-Version, or X-AspNet-Version)</li></ul>	<span>HELO MYDOMAIN<br></span><ol><li>\r\nMAIL FROM:&lt;InternalName1@domain.ch&gt;</li><li>RCPT TO :&lt;InternalName2@domain.ch&gt;</li></ol><span>\r\nREPLY-TO:&lt;infoguard@netprotect.ch)<br>\r\nData<br></span><ul><li>\r\nFROM: InternalName1</li><li>TO: InternalName2</li></ul><span><span>\r\nSubject: Infoguard Test <br>\r\n<br>\r\nGruezi!<br>\r\n<br>\r\n</span><span>Dies ist ein Mail Spoofing Check von Infoguard. Wir\r\nversuchen dabei von extern auf dem Mailserver des Kunden zu verbinden und im\r\nNamen eines existierenden internen Mitarbeiters A eine Mail an einen internen\r\nMitarbeiter B zu senden. Bitte um kurze Rueckbestaetigung, falls diese Mail\r\nangekommen ist (infoguard@netprotect.ch). <br>\r\n<br>\r\n</span><span>Gruss<br></span></span><ul><li>\r\nInfoguard AG</li></ul>	f	t	t		\N	<span>HELO MYDOMAIN<br></span><ul><li>\r\nMAIL FROM:&lt;InternalName1@domain.ch&gt;</li><li>RCPT TO :&lt;InternalName2@domain.ch&gt;</li></ul><span>\r\nREPLY-TO:&lt;infoguard@netprotect.ch)<br>\r\nData<br>\r\n<br></span><ol><li>\r\nFROM: InternalName1</li><li>TO: InternalName2</li></ol><span><span>\r\nSubject: Infoguard Test <br>\r\n<br>\r\nGruezi!<br>\r\n<br>\r\n</span><span>Dies ist ein Mail Spoofing Check von Infoguard. Wir\r\nversuchen dabei von extern auf dem Mailserver des Kunden zu verbinden und im\r\nNamen eines existierenden internen Mitarbeiters A eine Mail an einen internen\r\nMitarbeiter B zu senden. Bitte um kurze Rueckbestaetigung, falls diese Mail\r\nangekommen ist (infoguard@netprotect.ch). <br>\r\n<br>\r\n</span><span>Gruss<br>\r\nInfoguard AG</span></span>	1			2	46
56	1	DNS Hosting (Copy)	hello		f	t	f	adfasd	\N		1			2	56
22	4	SSH Bruteforce			f	t	f		\N		1			2	22
23	5	Nmap Port Scan			f	t	f		\N		1			2	23
24	5	TCP Port Scan			f	t	f		\N		1			2	24
25	5	TCP Traceroute			f	t	f		80		1			2	25
26	6	Apache DoS			f	t	f		\N		1			2	26
27	6	Fuzz Check			f	t	f		\N		1			2	27
28	6	Google URL			f	t	f		\N		1			2	28
29	6	Grep URL			f	t	f	http	\N		1			2	29
30	6	HTTP Banner			f	t	f	http	\N		1			2	30
31	6	Joomla Scan			f	t	f	http	\N		1			2	31
32	6	Login Pages			f	t	f	http	\N		1			2	32
33	6	Nikto			f	t	f	http	80		1			2	33
34	6	URL Scan			f	t	f	http	\N		1			2	34
35	6	Web Auth Scanner			f	t	f	http	80		1			2	35
36	6	Web Directory Scanner			f	t	f	http	80		1			2	36
37	6	Web File Scanner			f	t	f	http	80		1			2	37
38	6	Web HTTP Methods			f	t	f		\N		1			2	38
39	6	Web Server CMS			f	t	f		\N		1			2	39
40	6	Web Server Error Message			f	t	f		\N		1			2	40
41	6	Web Server Files			f	t	f		\N		1			2	41
42	6	Web Server SSL			f	t	f		\N		1			2	42
43	6	Web SQL XSS			f	t	f		\N		1			2	43
5	7	DNS Find NS			f	t	f		\N		1			2	5
8	8	DNS NIC Typosquatting			f	t	f		\N		1			2	8
11	8	DNS Resolve IP			f	t	f		\N		1			2	11
14	9	DNS SPF (Perl)			f	t	f		\N		1			2	14
45	1	DNS A (Non-Recursive)			f	t	f		\N		1			2	3
6	1	DNS Hosting	hello		f	t	f	adfasd	\N		1			2	9
3	1	DNS AFXR	hey <b>fuck \\' sss</b><br><b>How are you?<br></b>sd<br><b></b>1. this is some kind of list<br>2. lololo upup up<br>sdfa<br>asdf<br>asdf<br>sdd<br>sdf<br>sdf	jjj<br>what the fuck did you do?	f	t	f		\N	No more no more	1			2	7
1	1	DNS A	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.		f	t	f		\N		1			2	0
47	12	CMS check			f	t	f	http	80		1			2	47
48	10	yay			f	f	f		\N		1			2	48
49	13	hh			f	f	f		\N		1			2	49
55	1	DNS A (Copy)	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.		f	t	f		\N		1			2	55
50	1	DNS TEST	test		f	f	f		\N		1			2	50
\.


--
-- Data for Name: checks_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks_l10n (check_id, language_id, name, background_info, hints, reference, question) FROM stdin;
7	1	DNS IP Range	\N	\N	\N	\N
7	2	\N	\N	\N	\N	\N
9	1	DNS NIC Whois	\N	\N	\N	\N
9	2	\N	\N	\N	\N	\N
10	1	DNS NS Version	\N	\N	\N	\N
10	2	\N	\N	\N	\N	\N
12	1	DNS SOA	\N	\N	\N	\N
12	2	\N	\N	\N	\N	\N
13	1	DNS SPF	\N	\N	\N	\N
13	2	\N	\N	\N	\N	\N
15	1	DNS Subdomain Bruteforce	\N	\N	\N	\N
15	2	\N	\N	\N	\N	\N
16	1	DNS Top TLDs	\N	\N	\N	\N
16	2	\N	\N	\N	\N	\N
17	1	FTP Bruteforce	\N	\N	\N	\N
17	2	\N	\N	\N	\N	\N
18	1	SMTP Banner	\N	\N	\N	\N
18	2	\N	\N	\N	\N	\N
19	1	SMTP DNSBL	\N	\N	\N	\N
19	2	\N	\N	\N	\N	\N
20	1	SMTP Filter	\N	\N	\N	\N
20	2	\N	\N	\N	\N	\N
21	1	SMTP Relay	\N	\N	\N	\N
21	2	\N	\N	\N	\N	\N
22	1	SSH Bruteforce	\N	\N	\N	\N
22	2	\N	\N	\N	\N	\N
23	1	Nmap Port Scan	\N	\N	\N	\N
23	2	\N	\N	\N	\N	\N
24	1	TCP Port Scan	\N	\N	\N	\N
24	2	\N	\N	\N	\N	\N
25	1	TCP Traceroute	\N	\N	\N	\N
25	2	\N	\N	\N	\N	\N
26	1	Apache DoS	\N	\N	\N	\N
26	2	\N	\N	\N	\N	\N
27	1	Fuzz Check	\N	\N	\N	\N
27	2	\N	\N	\N	\N	\N
28	1	Google URL	\N	\N	\N	\N
28	2	\N	\N	\N	\N	\N
29	1	Grep URL	\N	\N	\N	\N
29	2	\N	\N	\N	\N	\N
30	1	HTTP Banner	\N	\N	\N	\N
30	2	\N	\N	\N	\N	\N
31	1	Joomla Scan	\N	\N	\N	\N
31	2	\N	\N	\N	\N	\N
32	1	Login Pages	\N	\N	\N	\N
32	2	\N	\N	\N	\N	\N
33	1	Nikto	\N	\N	\N	\N
33	2	\N	\N	\N	\N	\N
34	1	URL Scan	\N	\N	\N	\N
34	2	\N	\N	\N	\N	\N
35	1	Web Auth Scanner	\N	\N	\N	\N
35	2	\N	\N	\N	\N	\N
36	1	Web Directory Scanner	\N	\N	\N	\N
36	2	\N	\N	\N	\N	\N
37	1	Web File Scanner	\N	\N	\N	\N
37	2	\N	\N	\N	\N	\N
38	1	Web HTTP Methods	\N	\N	\N	\N
38	2	\N	\N	\N	\N	\N
39	1	Web Server CMS	\N	\N	\N	\N
39	2	\N	\N	\N	\N	\N
40	1	Web Server Error Message	\N	\N	\N	\N
40	2	\N	\N	\N	\N	\N
41	1	Web Server Files	\N	\N	\N	\N
41	2	\N	\N	\N	\N	\N
42	1	Web Server SSL	\N	\N	\N	\N
42	2	\N	\N	\N	\N	\N
43	1	Web SQL XSS	\N	\N	\N	\N
43	2	\N	\N	\N	\N	\N
5	1	DNS Find NS	\N	\N	\N	\N
5	2	\N	\N	\N	\N	\N
8	1	DNS NIC Typosquatting	\N	\N	\N	\N
8	2	\N	\N	\N	\N	\N
11	1	DNS Resolve IP	\N	\N	\N	\N
11	2	\N	\N	\N	\N	\N
14	1	DNS SPF (Perl)	\N	\N	\N	\N
14	2	\N	\N	\N	\N	\N
45	1	DNS A (Non-Recursive)	\N	\N	\N	\N
45	2	\N	\N	\N	\N	\N
6	1	DNS Hosting	hello	\N	\N	\N
6	2	\N	\N	\N	\N	\N
3	1	DNS AFXR	hey <b>fuck \\' sss</b><br><b>How are you?<br></b>sd<br><b></b>1. this is some kind of list<br>2. lololo upup up<br>sdfa<br>asdf<br>asdf<br>sdd<br>sdf<br>sdf	jjj<br>what the fuck did you do?	\N	No more no more
3	2	ZXZXZXX	meowsfvfd<br>sdfsdf<br>sdf<br>sdf<br>sd<br>f<br>sdf<br>sd<br>f<br>sd<br>f<br>sdf	piu<i> poiu</i>	\N	\N
1	1	DNS A	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.	\N	\N	\N
1	2	ZZZ	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.	\N	\N	\N
46	2	\N	\N	\N	\N	\N
46	1	Scan Somethingh	<b>Background Info:</b><br>The http-server headers often leak information about servertype and versions. In our check we request the server via http 1.0 with and without host-header to check for misconfigured vhosts and via a usual http 1.1-request. Having the servertype and version an attacker can start investigating if there are known exploits for that specific version. This helps the attacker to prepare all targeted attacks. We will look specifically for the following tags:<br><br><ul><li>Server: A name for the server including sometimes the version.</li><li>Via: Informs the client of proxies through which the response was sent.</li><li>X-Powered-By: specifies the technology (e.g. ASP.NET, PHP, JBoss) supporting the web application (version details are often in X-Runtime, X-Version, or X-AspNet-Version)</li></ul>	<span>HELO MYDOMAIN<br></span><ol><li>\r\nMAIL FROM:&lt;InternalName1@domain.ch&gt;</li><li>RCPT TO :&lt;InternalName2@domain.ch&gt;</li></ol><span>\r\nREPLY-TO:&lt;infoguard@netprotect.ch)<br>\r\nData<br></span><ul><li>\r\nFROM: InternalName1</li><li>TO: InternalName2</li></ul><span><span>\r\nSubject: Infoguard Test <br>\r\n<br>\r\nGruezi!<br>\r\n<br>\r\n</span><span>Dies ist ein Mail Spoofing Check von Infoguard. Wir\r\nversuchen dabei von extern auf dem Mailserver des Kunden zu verbinden und im\r\nNamen eines existierenden internen Mitarbeiters A eine Mail an einen internen\r\nMitarbeiter B zu senden. Bitte um kurze Rueckbestaetigung, falls diese Mail\r\nangekommen ist (infoguard@netprotect.ch). <br>\r\n<br>\r\n</span><span>Gruss<br></span></span><ul><li>\r\nInfoguard AG</li></ul>	\N	<span>HELO MYDOMAIN<br></span><ul><li>\r\nMAIL FROM:&lt;InternalName1@domain.ch&gt;</li><li>RCPT TO :&lt;InternalName2@domain.ch&gt;</li></ul><span>\r\nREPLY-TO:&lt;infoguard@netprotect.ch)<br>\r\nData<br>\r\n<br></span><ol><li>\r\nFROM: InternalName1</li><li>TO: InternalName2</li></ol><span><span>\r\nSubject: Infoguard Test <br>\r\n<br>\r\nGruezi!<br>\r\n<br>\r\n</span><span>Dies ist ein Mail Spoofing Check von Infoguard. Wir\r\nversuchen dabei von extern auf dem Mailserver des Kunden zu verbinden und im\r\nNamen eines existierenden internen Mitarbeiters A eine Mail an einen internen\r\nMitarbeiter B zu senden. Bitte um kurze Rueckbestaetigung, falls diese Mail\r\nangekommen ist (infoguard@netprotect.ch). <br>\r\n<br>\r\n</span><span>Gruss<br>\r\nInfoguard AG</span></span>
50	1	DNS TEST	test	\N	\N	\N
47	1	CMS check	\N	\N	\N	\N
50	2	\N	\N	\N	\N	\N
47	2	\N	\N	\N	\N	\N
48	1	yay	\N	\N	\N	\N
48	2	\N	\N	\N	\N	\N
49	1	hh	\N	\N	\N	\N
49	2	\N	\N	\N	\N	\N
54	1	DNS A (Copy)	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.	\N	\N	\N
54	2	ZZZ (Copy)	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.	\N	\N	\N
55	1	DNS A (Copy)	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.	\N	\N	\N
55	2	ZZZ (Copy)	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.	\N	\N	\N
56	1	DNS Hosting (Copy)	hello	\N	\N	\N
56	2	 (Copy)	\N	\N	\N	\N
\.


--
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY clients (id, name, country, state, city, address, postcode, website, contact_name, contact_phone, contact_email, contact_fax, logo_path, logo_type) FROM stdin;
2	Ziga										\N	\N	\N
4	Helloy										123-123-123	\N	\N
1	Test	Switzerland		Zurich	Kallison Lane, 7	123456	http://netprotect.ch	Ivan John		invan@john.com	123-123-123	40453852965cebb2b0dbc5440323bea3f5adf750c8b5f72e06b5fc7a9aad9da4	image/png
\.


--
-- Data for Name: emails; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY emails (id, user_id, subject, content, attempts, sent) FROM stdin;
\.


--
-- Data for Name: gt_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_categories (id, name) FROM stdin;
2	Technical Test
\.


--
-- Data for Name: gt_categories_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_categories_l10n (gt_category_id, language_id, name) FROM stdin;
2	1	Technical Test
2	2	\N
\.


--
-- Data for Name: gt_check_dependencies; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_check_dependencies (id, gt_check_id, gt_module_id, condition) FROM stdin;
2	4	5	port=22
3	9	5	22
\.


--
-- Data for Name: gt_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_checks (id, gt_module_id, check_id, description, target_description, sort_order, gt_dependency_processor_id) FROM stdin;
6	4	3	bleble	bzebze	1	\N
7	5	13	bloblo	bzobzo	0	\N
8	5	17	blublu	bzubzu	1	\N
4	4	1			0	1
9	4	23	Blabla test		2	1
\.


--
-- Data for Name: gt_checks_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_checks_l10n (gt_check_id, language_id, description, target_description) FROM stdin;
4	1	\N	\N
4	2	\N	\N
9	1	Blabla test	asdfjlaksjd flkasdj fasdj flkasdj faksdj fklsadj flkjasd fj ;eworu tpwie;adfjsv.jdjdv
9	2	\N	\N
6	1	\N	\N
6	2	\N	\N
7	1	\N	\N
7	2	\N	\N
8	1	\N	\N
8	2	\N	\N
\.


--
-- Data for Name: gt_dependency_processors; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_dependency_processors (id, name) FROM stdin;
1	nmap-port
\.


--
-- Data for Name: gt_modules; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_modules (id, gt_type_id, name) FROM stdin;
3	7	Internal Penetration Test
4	7	External Penetration Test
5	8	OS Tests
\.


--
-- Data for Name: gt_modules_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_modules_l10n (gt_module_id, language_id, name) FROM stdin;
3	1	Internal Penetration Test
3	2	\N
4	1	External Penetration Test
4	2	\N
5	1	OS Tests
5	2	\N
\.


--
-- Data for Name: gt_types; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_types (id, gt_category_id, name) FROM stdin;
7	2	Network Based
8	2	System Based
\.


--
-- Data for Name: gt_types_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_types_l10n (gt_type_id, language_id, name) FROM stdin;
7	1	Network Based
7	2	\N
8	1	System Based
8	2	\N
\.


--
-- Data for Name: languages; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY languages (id, name, code, "default") FROM stdin;
1	English	en	t
2	Deutsch	de	f
\.


--
-- Data for Name: login_history; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY login_history (id, user_id, user_name, create_time) FROM stdin;
1	1	Oliver Muenchow	2013-01-20 19:00:34.601315
2	1	Oliver Muenchow	2013-01-20 19:00:46.822096
3	1	Oliver Muenchow	2013-01-21 00:36:16.786873
4	1	Oliver Muenchow	2013-01-21 00:36:48.513369
5	1	Oliver Muenchow	2013-01-21 01:39:25.282192
6	\N	test@client.com	2013-01-21 01:41:18.967625
7	1	Oliver Muenchow	2013-01-21 01:41:35.632161
118	1	Oliver Muenchow	2013-05-29 11:07:37.00291
8	1	Oliver Muenchow	2013-01-21 12:45:25.103189
9	1	Oliver Muenchow	2013-01-21 18:55:41.217336
10	1	Oliver Muenchow	2013-01-22 00:09:47.837418
11	1	Oliver Muenchow	2013-01-22 12:48:55.146417
12	1	Oliver Muenchow	2013-01-22 14:48:23.294127
13	1	Oliver Muenchow	2013-01-22 19:29:43.01782
14	1	Oliver Muenchow	2013-01-23 17:36:02.634146
15	1	Oliver Muenchow	2013-01-24 06:05:48.900624
16	1	Oliver Muenchow	2013-01-24 19:51:32.786624
17	1	Oliver Muenchow	2013-02-07 16:25:53.558536
18	1	Oliver Muenchow	2013-02-08 16:11:20.123214
19	1	Oliver Muenchow	2013-02-08 19:36:41.043217
20	1	Oliver Muenchow	2013-02-09 02:51:28.691028
21	1	Oliver Muenchow	2013-02-09 02:59:42.890879
22	1	Oliver Muenchow	2013-02-09 04:40:16.891014
23	1	Oliver Muenchow	2013-02-09 07:15:45.180509
24	1	Oliver Muenchow	2013-02-18 04:03:35.828318
25	1	Oliver Muenchow	2013-02-18 13:23:46.080472
26	1	Oliver Muenchow	2013-02-20 17:36:22.531282
27	1	Oliver Muenchow	2013-03-07 20:46:32.692731
28	1	Oliver Muenchow	2013-04-17 12:34:46.254237
29	1	Oliver Muenchow	2013-05-04 16:21:28.136106
30	3	erbol@gmail.com	2013-05-04 16:21:42.171008
31	1	Oliver Muenchow	2013-05-04 16:22:04.624138
32	3	erbol@gmail.com	2013-05-04 16:29:49.91426
33	1	Oliver Muenchow	2013-05-04 16:30:00.750086
34	3	erbol@gmail.com	2013-05-04 16:30:27.665802
35	1	Oliver Muenchow	2013-05-04 16:31:02.818463
36	3	erbol@gmail.com	2013-05-04 16:32:27.164168
37	1	Oliver Muenchow	2013-05-04 16:58:10.953036
38	3	erbol@gmail.com	2013-05-04 17:47:53.349979
39	1	Oliver Muenchow	2013-05-04 18:02:57.663391
40	4	Anton Belousov	2013-05-04 18:22:40.866149
41	1	Oliver Muenchow	2013-05-04 18:24:03.418928
42	1	Oliver Muenchow	2013-05-05 00:00:47.46133
43	1	Oliver Muenchow	2013-05-05 01:41:26.172397
44	1	Oliver Muenchow	2013-05-05 02:32:43.503971
45	1	Oliver Muenchow	2013-05-05 02:39:20.934522
46	1	Oliver Muenchow	2013-05-05 02:43:44.088959
47	1	Oliver Muenchow	2013-05-05 13:02:39.753248
48	1	Oliver Muenchow	2013-05-05 15:14:46.241212
49	1	Oliver Muenchow	2013-05-05 21:53:34.830308
50	1	Oliver Muenchow	2013-05-06 07:06:26.488798
51	1	Oliver Muenchow	2013-05-06 09:46:18.608852
52	1	Oliver Muenchow	2013-05-06 11:20:56.444186
53	1	Oliver Muenchow	2013-05-07 00:02:45.951627
54	3	erbol@gmail.com	2013-05-07 01:09:45.207636
55	1	Oliver Muenchow	2013-05-10 14:15:49.369777
56	1	Oliver Muenchow	2013-05-12 14:14:48.709753
57	3	erbol@gmail.com	2013-05-12 14:20:55.225847
58	1	Oliver Muenchow	2013-05-12 14:21:22.705745
59	3	erbol@gmail.com	2013-05-12 14:21:38.308358
60	1	Oliver Muenchow	2013-05-12 14:43:57.73245
61	1	Oliver Muenchow	2013-05-12 14:44:13.23226
62	3	erbol@gmail.com	2013-05-12 14:44:26.724086
63	3	erbol@gmail.com	2013-05-13 04:24:57.650017
64	1	Oliver Muenchow	2013-05-13 04:42:46.659082
65	1	Oliver Muenchow	2013-05-13 06:49:37.443378
66	1	Oliver Muenchow	2013-05-13 06:53:20.382185
67	1	Oliver Muenchow	2013-05-13 06:54:03.532392
68	1	Oliver Muenchow	2013-05-13 06:54:10.715511
69	1	Oliver Muenchow	2013-05-13 06:54:37.632413
70	1	Oliver Muenchow	2013-05-13 06:55:04.648358
71	1	Oliver Muenchow	2013-05-13 06:55:31.848323
72	1	Oliver Muenchow	2013-05-13 06:58:00.675492
73	1	Oliver Muenchow	2013-05-13 06:58:32.454467
74	1	Oliver Muenchow	2013-05-13 06:58:59.610713
75	1	Oliver Muenchow	2013-05-13 06:59:09.381016
76	1	Oliver Muenchow	2013-05-13 07:01:11.860036
77	3	erbol@gmail.com	2013-05-13 07:01:16.27627
78	1	Oliver Muenchow	2013-05-13 07:09:00.013513
79	1	Oliver Muenchow	2013-05-13 07:09:00.126513
80	1	Oliver Muenchow	2013-05-13 07:14:06.042734
81	1	Oliver Muenchow	2013-05-13 07:14:35.116576
82	1	Oliver Muenchow	2013-05-13 07:16:55.641998
83	1	Oliver Muenchow	2013-05-13 09:40:00.374686
84	1	Oliver Muenchow	2013-05-13 09:50:50.611682
85	1	Oliver Muenchow	2013-05-13 14:30:44.956955
86	1	Oliver Muenchow	2013-05-13 14:51:24.786528
87	1	Oliver Muenchow	2013-05-13 14:52:51.947255
88	1	Oliver Muenchow	2013-05-13 15:43:56.439016
89	1	Oliver Muenchow	2013-05-13 15:45:52.856195
90	3	erbol@gmail.com	2013-05-13 15:54:37.959878
91	1	Oliver Muenchow	2013-05-13 15:54:51.981638
92	1	Oliver Muenchow	2013-05-21 03:19:49.267786
93	1	Oliver Muenchow	2013-05-21 06:39:23.414544
94	1	Oliver Muenchow	2013-05-21 17:54:52.59129
95	1	Oliver Muenchow	2013-05-22 04:50:21.244905
96	1	Oliver Muenchow	2013-05-22 11:46:33.133179
97	1	Oliver Muenchow	2013-05-22 13:30:32.315321
98	1	Oliver Muenchow	2013-05-22 18:35:13.894266
99	1	Oliver Muenchow	2013-05-22 21:38:19.855895
100	1	Oliver Muenchow	2013-05-23 00:52:35.79066
101	1	Oliver Muenchow	2013-05-23 04:20:05.404432
102	1	Oliver Muenchow	2013-05-23 15:10:18.450919
103	1	Oliver Muenchow	2013-05-24 04:14:08.627662
104	1	Oliver Muenchow	2013-05-24 16:10:35.56003
105	1	Oliver Muenchow	2013-05-24 20:37:26.399811
106	1	Oliver Muenchow	2013-05-25 02:39:58.116332
107	1	Oliver Muenchow	2013-05-25 08:09:36.693424
108	1	Oliver Muenchow	2013-05-25 17:31:13.70335
109	1	Oliver Muenchow	2013-05-25 20:52:57.60592
110	1	Oliver Muenchow	2013-05-27 06:08:21.481453
111	1	Oliver Muenchow	2013-05-27 14:08:12.941622
112	1	Oliver Muenchow	2013-05-27 15:09:45.09507
113	1	Oliver Muenchow	2013-05-28 07:12:21.374359
114	1	Oliver Muenchow	2013-05-28 09:32:58.2842
115	1	Oliver Muenchow	2013-05-29 03:14:30.392082
116	1	Oliver Muenchow	2013-05-29 06:55:26.939293
117	1	Oliver Muenchow	2013-05-29 07:25:53.866002
119	1	Oliver Muenchow	2013-05-29 13:15:25.475611
120	1	Oliver Muenchow	2013-05-29 14:55:46.22886
121	1	Oliver Muenchow	2013-05-29 18:21:52.530181
122	1	Oliver Muenchow	2013-05-30 21:36:38.334615
123	1	Oliver Muenchow	2013-05-31 00:56:34.665394
124	1	Oliver Muenchow	2013-05-31 06:23:26.56117
125	1	Oliver Muenchow	2013-05-31 19:59:10.91552
126	1	Oliver Muenchow	2013-06-01 02:14:31.966267
127	1	Oliver Muenchow	2013-06-01 03:27:52.78245
128	1	Oliver Muenchow	2013-06-01 17:56:45.41614
129	1	Oliver Muenchow	2013-06-02 05:24:53.711565
130	1	Oliver Muenchow	2013-06-02 16:24:58.435259
\.


--
-- Data for Name: project_details; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_details (id, project_id, subject, content) FROM stdin;
2	1	hello	world
3	2	kekek	kkk\r\n
4	1	yaya	aazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzzaazzzzzzzzzzzzzz
\.


--
-- Data for Name: project_gt_check_attachments; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_gt_check_attachments (project_id, gt_check_id, name, type, path, size) FROM stdin;
\.


--
-- Data for Name: project_gt_check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_gt_check_inputs (project_id, gt_check_id, check_input_id, value, file) FROM stdin;
8	4	1	asdfasdf	f61af7bdcbb79fbf59f55ef807e0a05203102e984802ef2c1cb6a95ef6cf2db2
8	4	61	\N	13c14eadf5e69bd7a313c70f7bb604a3d105f3eaa60b21d7e580e8d791143d63
8	9	28	22,80,443	0ca75eac3a8921fce54b617695ecfa3fe9e28d5a8f000af3841c7159f1495f8f
8	9	29	0	ecbf7e64c960e580fd6dafeb8f26cac14673d71125e2187b7b266f0f9840ba87
8	9	65	0	21a881ee6c18009a0fce3e1c655f63781ead90b62ee92cefd32ee24f3dd76c49
8	9	66	0	848c98287f6368a7650a3cce40beb88b4db0ef414b83ad43479cf6e5885270ea
8	9	67	2	5b91e8169b0484b890949db0e4ab9d42ef88fcd18f59e2415c5b1f7fea65cc50
8	9	68	1	2fa32296956ed127df9debb46773022b420173cfb134118ea97b28b723356c47
\.


--
-- Data for Name: project_gt_check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_gt_check_solutions (project_id, gt_check_id, check_solution_id) FROM stdin;
\.


--
-- Data for Name: project_gt_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_gt_checks (project_id, gt_check_id, user_id, language_id, target, port, protocol, target_file, result_file, result, table_result, started, pid, rating, status) FROM stdin;
8	4	1	1	test.com	\N	\N	20529736a1df2733a84253167eb8f2f678b4c04083fec06c624dfb90ef80eff3	1195d986b42031cca73daf33df75581c9c17f8afc38f3971cd4f76f639089da4	dns_a.py\n--------\n174.36.85.72\n\ndns_a.py\n--------\n174.36.85.72\n\ndns_a.py\n--------\n174.36.85.72\n\ndns_a.py\n--------\n174.36.85.72\n\ndns_a.py\n--------\n174.36.85.72\n\ndns_a.py\n--------\n174.36.85.72\nNo output.\ndns_a.py\n--------\n174.36.85.72\n\ndns_a.py\n--------\n174.36.85.72\n\ndns_a_nr.py\n-----------\nNoHostName: No host name specified.\n\ndns_a.py\n--------\n174.36.85.72\n\ndns_a_nr.py\n-----------\nNoHostName: No host name specified.\n\ndns_a.py\n--------\n174.36.85.72\n\ndns_a_nr.py\n-----------\nNoHostName: No host name specified.\n\ndns_a.py\n--------\n174.36.85.72\n\ndns_a_nr.py\n-----------\nNoHostName: No host name specified.\n\ndns_a.py\n--------\n174.36.85.72\n\ndns_a_nr.py\n-----------\nNoHostName: No host name specified.\n\ndns_a.py\n--------\n174.36.85.72\n\ndns_a_nr.py\n-----------\nNoHostName: No host name specified.\n	\N	2013-06-02 18:41:09	\N	\N	finished
8	6	1	1	\N	\N	\N	\N	\N	\N	\N	\N	\N	med_risk	finished
8	9	1	1	teremok-finance.ru	\N	\N	2518fef19e1c4a9d29c9a07e5edd1222b936bc39a5afb7c427639461698c45ab	3b21b63550a79dc46633c923c2437ff6ca3208a4330f117e87c6b93d4c2b9583	nmap_tcp.pl\n-----------\n\nStarting Nmap 5.00 ( http://nmap.org ) at 2013-06-02 21:23 MSK\nInteresting ports on static.166.202.46.78.clients.your-server.de (78.46.202.166):\nPORT    STATE SERVICE\n22/tcp  open  ssh\n80/tcp  open  http\n443/tcp open  https\n\nNmap done: 1 IP address (1 host up) scanned in 1.91 seconds\n\nnmap_tcp.pl\n-----------\n\nnmap_tcp.pl\n-----------\n	<gtta-table><columns><column width="0.3" name="Address"/><column width="0.2" name="Port"/><column width="0.2" name="Service"/><column width="0.3" name="Product"/></columns><row><cell>78.46.202.166 (static.166.202.46.78.clients.your-server.de)</cell><cell>22</cell><cell>ssh</cell><cell>N/A</cell></row><row><cell>78.46.202.166 (static.166.202.46.78.clients.your-server.de)</cell><cell>80</cell><cell>http</cell><cell>N/A</cell></row><row><cell>78.46.202.166 (static.166.202.46.78.clients.your-server.de)</cell><cell>443</cell><cell>https</cell><cell>N/A</cell></row></gtta-table><gtta-table><columns><column width="0.3" name="Address"/><column width="0.2" name="Port"/><column width="0.2" name="Service"/><column width="0.3" name="Product"/></columns><row><cell>94.75.237.147</cell><cell>22</cell><cell>ssh</cell><cell>N/A</cell></row><row><cell>94.75.237.147</cell><cell>80</cell><cell>http</cell><cell>N/A</cell></row></gtta-table>	2013-06-02 19:25:00	\N	\N	finished
\.


--
-- Data for Name: project_gt_modules; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_gt_modules (project_id, gt_module_id, sort_order) FROM stdin;
8	4	0
8	5	1
\.


--
-- Data for Name: project_gt_suggested_targets; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_gt_suggested_targets (id, project_id, gt_module_id, target, gt_check_id, approved) FROM stdin;
13	8	5	78.46.202.166	9	t
14	8	5	static.166.202.46.78.clients.your-server.de	9	t
15	8	5	94.75.237.147	9	t
\.


--
-- Data for Name: project_users; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_users (project_id, user_id, admin) FROM stdin;
13	1	t
1	3	f
13	4	f
\.


--
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY projects (id, client_id, year, deadline, name, status, vuln_overdue, guided_test) FROM stdin;
6	1	2012	2012-09-21	aaa	in_progress	\N	f
3	1	2012	2012-09-21	xxx	open	\N	f
4	2	2012	2012-09-21	yyy	open	\N	f
9	2	2012	2012-09-21	fff	open	\N	f
10	1	2012	2012-09-21	ddd	open	\N	f
11	2	2012	2012-09-21	eee	open	\N	f
12	2	2013	2012-09-21	kokokoko	open	\N	f
5	1	2012	2012-09-21	zzz	finished	\N	f
2	2	2012	2012-07-29	Fuck	finished	\N	f
1	2	2012	2012-07-27	Test	in_progress	2012-09-28	f
13	4	2012	2013-02-09	Buka	open	\N	f
8	2	2012	2012-09-21	ccc	in_progress	\N	t
\.


--
-- Data for Name: references; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY "references" (id, name, url) FROM stdin;
1	CUSTOM	
\.


--
-- Data for Name: report_template_sections; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_template_sections (id, report_template_id, check_category_id, intro, sort_order, title) FROM stdin;
1	1	9	Project admin:&nbsp;{admin} hey<br><br>OLOLO<br><ul><li>все вы быдло</li><li>и хуйло</li></ul><br>	0	Hey
4	1	1	key key	1	Key
\.


--
-- Data for Name: report_template_sections_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_template_sections_l10n (report_template_section_id, language_id, intro, title) FROM stdin;
1	1	Project admin:&nbsp;{admin} hey<br><br>OLOLO<br><ul><li>все вы быдло</li><li>и хуйло</li></ul><br>	Hey
1	2	Project admin:&nbsp;{admin}	kkkkk
4	1	key key	Key
4	2	\N	\N
\.


--
-- Data for Name: report_template_summary; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_template_summary (id, summary, rating_from, rating_to, report_template_id, title) FROM stdin;
3	The general security state of the infrastructure is rated with a “{rating}”: low to medium critical”. This is a cumulative value that reflects the overall security\r\nstatus. Only a few problems can cause a severe impact. Therefore this value is\r\ndriven mainly by the vulnerabilities within a few devices.<br><br>Some of the vulnerabilities are critical. But none of them would help an\r\nattacker to immediately take over a system. Client "{client}" still has to be aware that this is only\r\na snapshot of the current situation. Any change in the future (like new\r\nexploits available for a specific system) could change the situation.&nbsp;<br><br><ul><li>list</li><li>goes here</li></ul><br>and here is a numbered list<br><ol><li>one</li><li>two</li></ol>yay<br>	0.00	5.00	1	Everything is fine!
4		1.00	2.00	1	Hello
\.


--
-- Data for Name: report_template_summary_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_template_summary_l10n (report_template_summary_id, language_id, summary, title) FROM stdin;
3	1	The general security state of the infrastructure is rated with a “{rating}”: low to medium critical”. This is a cumulative value that reflects the overall security\r\nstatus. Only a few problems can cause a severe impact. Therefore this value is\r\ndriven mainly by the vulnerabilities within a few devices.<br><br>Some of the vulnerabilities are critical. But none of them would help an\r\nattacker to immediately take over a system. Client "{client}" still has to be aware that this is only\r\na snapshot of the current situation. Any change in the future (like new\r\nexploits available for a specific system) could change the situation.&nbsp;<br><br><ul><li>list</li><li>goes here</li></ul><br>and here is a numbered list<br><ol><li>one</li><li>two</li></ol>yay<br>	Everything is fine!
3	2	\N	\N
4	1	\N	Hello
4	2	\N	\N
\.


--
-- Data for Name: report_templates; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_templates (id, name, header_image_path, header_image_type, intro, appendix, vulns_intro, info_checks_intro, security_level_intro, vuln_distribution_intro, reduced_intro, high_description, med_description, low_description, degree_intro, risk_intro, footer) FROM stdin;
1	Test Template	0caf7534e0fee7a603c2948652ab8de6815ccea0b277340d7122269f4a847c89	image/png	Test Template Intro<br><ul><li>The client is: {client}</li><li>The project is: {project}</li><li>Project year:&nbsp;<b>{year}</b></li></ul>Project deadline: {deadline}<br>Project admin: {admin}<br>Project rating: {rating}<br><ol><li>Date from: {date.from}</li><li>Date to: {date.to}</li></ol>Targets: {targets}<br><br><b>Here's a list of targets:</b><br>{target.list}<br>This text goes after the list of targets.<br><b>well done<br><br>S-tats<br>{target.stats}<br><br>And here is a list of targets with controls lol:<br></b>{target.weakest}<br><b><br></b>number of checks: {checks} (info: {checks.info}, low: {checks.lo}, med: {checks.med}, high: {checks.hi})<br><b><br></b><b>Here go top 5 vulns:<br></b>{vuln.list}<b><br></b>well done	Test Template Appendix<br><ol><li>one</li><li>two</li><li>three</li></ol>	World&nbsp;{client}	Info Checks go here ;)&nbsp;{client}	test one two {targets}<br><br><ul><li>hello</li><li>wtf is that</li></ul>No way<br><ol><li>one list</li><li>two lists</li></ol>	test one two&nbsp;{targets}<br><br>vuln distribution<br><br>with<br><ul><li>some</li><li>list</li></ul>	reduced targets {targets}	high risk&nbsp;targets {targets}	med risk&nbsp;targets {targets}	low risk&nbsp;targets {targets}	degree&nbsp;targets {targets}<br><br><ol><li>degree</li><li>list</li></ol>	risk&nbsp;targets {targets}<br><br>risk<br><ul><li>matrix</li><li>list</li></ul>	1234 67 890 00 fuck
3	Yay ;)	\N	\N					\N	\N	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: report_templates_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_templates_l10n (report_template_id, language_id, name, intro, appendix, vulns_intro, info_checks_intro, security_level_intro, vuln_distribution_intro, reduced_intro, high_description, med_description, low_description, degree_intro, risk_intro, footer) FROM stdin;
1	2	zzz	Testen Templaten Intro	Testen Templaten Appendix	Worlda	\N	test eins zwei&nbsp;{targets}	test eins zwei&nbsp;{targets}	\N	\N	\N	\N	\N	\N	deutsche footer
1	1	Test Template	Test Template Intro<br><ul><li>The client is: {client}</li><li>The project is: {project}</li><li>Project year:&nbsp;<b>{year}</b></li></ul>Project deadline: {deadline}<br>Project admin: {admin}<br>Project rating: {rating}<br><ol><li>Date from: {date.from}</li><li>Date to: {date.to}</li></ol>Targets: {targets}<br><br><b>Here's a list of targets:</b><br>{target.list}<br>This text goes after the list of targets.<br><b>well done<br><br>S-tats<br>{target.stats}<br><br>And here is a list of targets with controls lol:<br></b>{target.weakest}<br><b><br></b>number of checks: {checks} (info: {checks.info}, low: {checks.lo}, med: {checks.med}, high: {checks.hi})<br><b><br></b><b>Here go top 5 vulns:<br></b>{vuln.list}<b><br></b>well done	Test Template Appendix<br><ol><li>one</li><li>two</li><li>three</li></ol>	World&nbsp;{client}	Info Checks go here ;)&nbsp;{client}	test one two {targets}<br><br><ul><li>hello</li><li>wtf is that</li></ul>No way<br><ol><li>one list</li><li>two lists</li></ol>	test one two&nbsp;{targets}<br><br>vuln distribution<br><br>with<br><ul><li>some</li><li>list</li></ul>	reduced targets {targets}	high risk&nbsp;targets {targets}	med risk&nbsp;targets {targets}	low risk&nbsp;targets {targets}	degree&nbsp;targets {targets}<br><br><ol><li>degree</li><li>list</li></ol>	risk&nbsp;targets {targets}<br><br>risk<br><ul><li>matrix</li><li>list</li></ul>	1234 67 890 00 fuck
3	1	Yay ;)	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
3	2	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: risk_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_categories (id, name, risk_template_id) FROM stdin;
20	Pipa	6
\.


--
-- Data for Name: risk_categories_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_categories_l10n (risk_category_id, language_id, name) FROM stdin;
20	1	Pipa
20	2	\N
\.


--
-- Data for Name: risk_category_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_category_checks (risk_category_id, check_id, damage, likelihood) FROM stdin;
20	46	1	1
20	48	1	1
20	49	1	1
20	1	1	1
20	3	1	1
20	45	1	1
20	6	1	1
20	7	1	1
20	9	1	1
20	10	1	1
20	12	1	1
20	13	1	1
20	15	1	1
20	16	1	1
20	8	1	1
20	11	1	1
20	14	1	1
20	5	1	1
20	17	1	1
20	47	1	1
20	18	1	1
20	19	1	1
20	20	1	1
20	21	1	1
20	22	1	1
20	23	1	1
20	24	1	1
20	25	1	1
20	26	1	1
20	27	1	1
20	28	1	1
20	29	1	1
20	30	1	1
20	31	1	1
20	32	1	1
20	33	1	1
20	34	1	1
20	35	1	1
20	36	1	1
20	37	1	1
20	38	1	1
20	39	1	1
20	40	1	1
20	41	1	1
20	42	1	1
20	43	1	1
\.


--
-- Data for Name: risk_templates; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_templates (id, name) FROM stdin;
6	test
\.


--
-- Data for Name: risk_templates_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_templates_l10n (risk_template_id, language_id, name) FROM stdin;
6	1	test
6	2	\N
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY sessions (id, expire, data) FROM stdin;
d8vj3ariknr5e4ukjdc6n1eba1      	1370194627	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
5vrm29tc0aar5odl1ggpbqt503      	1370194771	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
hh14vde5rtrg8i9d0t6dtkkq25      	1370194823	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
5gtcuo9idsa5pc3bgh92jgp5o6      	1370194828	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
j71qavnsnhjdof43b8hs03joc4      	1370194853	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
lq8fimvt39n52t23fv2vamkct2      	1370194904	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
4bvn4t08e4qfra4rs6v12v9sa7      	1370194918	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
seggeupnuogq4j1u9mt59c0ie2      	1370195108	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
51hc0ps1cfkvj8rnulo7hnd734      	1370195132	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
t6vsn3gkj0relptbptsj06a8f4      	1370195166	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
cr95qedj78d70bosdsae7ccri1      	1370195187	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
d26l4pd1jj4si7sce0vb2tudu6      	1370195207	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
csvu2lgfhhtrkib6r285u23qq3      	1370195251	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
qjthot41e98l0gr284e86enpp2      	1370195284	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
r5567pphhput7p570tto7jbj04      	1370195318	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
r559lvo42cofc377n8g6cmg5i3      	1370195335	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
ivmamods75co8jm256cl332kc0      	1370194356	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
1rvjspnrdoho44393hvnfc79s3      	1370195440	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
c4d2ph9ocdsppnv5gde8ao5297      	1370196382	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
s80qrp3bt54n5f1rvpae25mlk5      	1370196460	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
g5j7i566c6p67bmnpukhpljfl0      	1370196555	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
nqjvcg49e866uod2eddjudau30      	1370197127	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
7mmcshmvsj6mro8fmv4tjn7mg6      	1370197228	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
ggsgjanr0456tni5jfpl2di1u7      	1370197353	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
qosalslq4fjp3rkcn94nen4i62      	1370197403	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
g5ta3ql2qmbqddtu3p7270i432      	1370197498	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
thabloq5aokhb6a647v92kndd3      	1370197536	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
altag6tvcjch2t355bebtvu2o3      	1370197542	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
4v48qd0lkpmqsjmiuuatdc72i6      	1370197549	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
a16npbjnsvib1k7ugdv62gmco5      	1370197590	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
pvuqd8u6tq68tsej35buo7nc71      	1370197600	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
1i4khs056vd6h1s12ig2dljao4      	1370197611	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
8fa0ar12bkv72srn3dj8fi0n90      	1370197623	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
tjh2m138o057opv2th3nt6db15      	1370197654	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
p1stp92e9d47ri60cmt8pgrr66      	1370197659	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
p6nbe1ulfc5ol2bhj80ksuscs5      	1370197827	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
8l0u7rmuaet3316adv2t5v5gn0      	1370197840	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
8vmbc7e6dp6ep48dnh2s1044d7      	1370197846	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
plohq987acehgp37gucpmbr6a4      	1370194635	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
mo8hhsmu6p4iovfjp4o2avfog5      	1370194804	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
p0q79ml65tvp3kldfe3mtungs2      	1370194824	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
ii80od1pg9dnr8acd3uuj7plv5      	1370194829	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
7087vimg18im42ok4549utrcs1      	1370194867	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
oo14d3af30rnl30vj2hiic09n7      	1370194906	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
hp5v4k5p75jl9f224dodi3it32      	1370194921	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
j3nn72rik077fo8pjod5vnqfo4      	1370195111	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
bgeo70vjlqf8afv9apuv7ll4p6      	1370195152	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
3urhiubv1g9gtttlmdqe1figd4      	1370195172	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
mq7emnlcsgq3ass6bfgtlaaon7      	1370195189	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
kdpfsefm8n5quopg3fudal2384      	1370195222	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
i4qt1bt7sbu06388ea6qaqn7i5      	1370195254	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
jfi3ouc681ilip6uu1kbee67e4      	1370195286	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
isopv11nk1gm58s9iokhm238r2      	1370195321	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
cf1jm3gnq2m271oh1mc49b3dj1      	1370195339	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
0riiet2gabh87alld1h7q7mhp7      	1370195595	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
itkh8btoeqeapc2h8c6jg7deb7      	1370196388	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
oqhmqfdue7vbc69qag3sk8du13      	1370196512	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
g6fslg7vsfmc86hcgmi05td112      	1370196719	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
uuspn01s8j5pig6f0p4s9k2pk3      	1370197185	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
a2k8e3gg7vkt1ink8jq9ejo242      	1370197282	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
30n4v1fntq5sbksq2jreia0rk0      	1370197356	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
tb00hgsu9j63o91flln1s2si17      	1370197415	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
0lv1hiih0autuohi64s2entjd5      	1370197521	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
slg2g443q17dt0b0cr9ds4nt26      	1370197537	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
fa68rmll7jlqvi5c2io114oc23      	1370197544	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
8vt6ggn0679o8c6l7cktfg3bv0      	1370197586	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
pkje7bum418j2fp4sf80uuies1      	1370197591	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
180cdqia1a8bd61kee2m782nu1      	1370197603	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
0mgpm5i5b9cacb53ltodg5sbe3      	1370197615	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
5pccifgjlv1k002lsnp7khlck6      	1370197624	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
cq63at5gdfhcgp2eacj44vlba3      	1370197655	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
ig4il331qnnrm2rgq55ratfa44      	1370197806	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
sjgfcuch0ug3b9pd0l4sevnkd6      	1370197836	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
smmn0f4ehmptkjhq984b2qepk0      	1370197840	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
dnrml31pci8o13du9p42apn5c3      	1370197850	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
9r19cu83kt5hg66df2a8uiksh4      	1370194924	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
gm93sf43vc3sk62o463170ilg3      	1370194666	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
the9fcocbp7mlvb2111batbb34      	1370197588	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
7neh8o4ujbvtn5qc8ricq2m4i2      	1370194825	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
o01hroqtrisi3i8d3fnq3crtk0      	1370194876	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
n78h9h0i64hhrjugj492p0v7v0      	1370196414	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
1rn7nu9dj6imc7p71gpicjbm16      	1370195203	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
umv20cp6t1rmbi9ev6si4fh9a2      	1370195330	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
t710p9t5si86g7m3bnr9pro724      	1370194830	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
m8e3tvh8snmaitt1v74agc9rq4      	1370195120	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
1rji6beoe9u1r6hieat8vusl84      	1370194913	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
8b7bh3l9k1bd1sp8h2d2eoapj3      	1370195173	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
fvqljghpb5hab4o4f3ocrcob55      	1370194815	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
4arf2gf3611jgus8uu3fv2bku2      	1370197606	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
tepbo0ilbg7aovhv1j6u4tq4j0      	1370195257	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
8m1h3fal8o2db022qat045rko1      	1370197650	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
v0oql26iltpqkc1ramcpgm5l34      	1370197850	4bd35ae92c3475779675ebe8ae66f2fd__returnUrl|s:43:"/gt-template/2/type/7/module/4/check/4/edit";4bd35ae92c3475779675ebe8ae66f2fd__id|i:1;4bd35ae92c3475779675ebe8ae66f2fd__name|s:26:"erbol.turburgaev@gmail.com";4bd35ae92c3475779675ebe8ae66f2fd__states|a:0:{}4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
3vdlklc1fada8r9tvanrbe4cd6      	1370195154	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
ocfm4l01ricub4ab8ht8drujv3      	1370195307	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
pe7kiecahgvi501mjt7guhrnu0      	1370195237	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
o85ljm4b6e68g2o9hqir1ulde1      	1370194344	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
53hv87n5dasu79uboalh2f6f75      	1370196277	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
t1gon7eehng7pe2ptmg1lev185      	1370195340	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
blk09g5afornc5godokl06pm15      	1370197546	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
a4jkb9g27fdpsmo1rjv77sqfb7      	1370197346	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
vbljnerdhm6ura2lf87a5q1cr1      	1370197054	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
434klvmqqf72fk2gh0v1mgv716      	1370197820	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
hn7bnu3gs9vd6ri7cg1mfp5gj2      	1370197529	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
bqr8j66mueneu6bd3qvs618fh3      	1370196514	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
l1ist6km2jumsis5fa1bs5kvo5      	1370197593	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
g48ohdmuovgjrriedqt54a7h70      	1370197221	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
5fuh68g9cpg9fbuc4jnq2pp116      	1370197427	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
rbdgh0q0q2m31a8j3krfpav7j1      	1370197851	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
10feq2c24a6q3ol06n87ssphj2      	1370197359	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
n0fqobluohqop1kmqbpt02tm71      	1370197617	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
h1h25rmhv8vpdal2rl42rv2f43      	1370197842	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
moio72niarrpud53sde4q1i1u7      	1370197538	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
mmp5gtorh2240m27skca0mjbf5      	1370197837	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
1ldfdrpusqjmrkm29nrnahuas2      	1370197656	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
a3lojrb7egtvnsjrh4rpmi3pi0      	1370194730	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
nrurv7s1tuphugj9cdvop5q207      	1370194822	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
0ph9oproi7l1o1ptu1escvcl94      	1370194827	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
neamcl6b86mrivtkfe2dgv8ro2      	1370194845	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
50g0ar50eos3kkkd8e13ipffp6      	1370194900	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
e8deoqm6ukqlsccfr9vf9s1hs0      	1370194915	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
9gpla3eokve8695m8qejp8aj41      	1370195103	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
h5ea2nukqd9svf4u92f9c0rsg4      	1370195124	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
24ak0vsjiosl5t8nrvs6d6hpf4      	1370195156	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
l28t7hilusrdjvionilu8jbn81      	1370195180	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
l7peco9rh6buuqr3rnqffmpu26      	1370195205	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
qb9d786c4asokhvd8o920fc4q0      	1370195250	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
k8r6lih5snnog2fgi5flh535m5      	1370195281	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
tmpbpbibs8gfqt2hqac3vosbr2      	1370195310	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
1c2ou4f6lrpk8rdabuh1mvff01      	1370195333	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
as9p5tr5eak8tjbj7vlq6hha95      	1370194345	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
oudit4n14v2o73efoiin2pm9c2      	1370195368	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
k4ntl5qemh3fmli1tnji71sh12      	1370196309	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
3925tlcvgn4c9viiga00eqd8a1      	1370196425	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
4bkvnukblek0o2kvr7e0vafvb6      	1370196548	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
m4ck8snurtseagekg6b6787ms5      	1370197071	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
47v1uumr6v722076uinvh4qpj7      	1370197224	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
sbch0cj0nn1hd6ctq85h729j20      	1370197350	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
ehd486lntbk04d94ufiqltcen7      	1370197381	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
ein2pm9k1j0qvl6mnjmomvce70      	1370197489	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
4asqu3kahr2bgjjd1u6jesa9i0      	1370197531	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
748rtgn5qpflormnpjhkfe3lq2      	1370197539	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
le6d918fbeg5rrevcmhv2m6gc7      	1370197547	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
9goqdig7es4ukbfcf6bmvtfeh5      	1370197589	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
urtmt809aem2v8mgk4iqqv3lp7      	1370197597	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
5u19agli40barvqqt973clju80      	1370197610	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
jr6r8f695i9dplevojkckflca2      	1370197618	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
4gg4bgcp0ubgf96to589iqfm62      	1370197652	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
1h8po1sr3m5bqs409mjrovqlg2      	1370197658	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
mh3oac8kv3kga0h00it182k9r3      	1370197823	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
fj1r00s15dis8vhji51mg33ai3      	1370197838	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
f0763jc359b34goffj7ihge3d0      	1370197845	4bd35ae92c3475779675ebe8ae66f2fdYii.CWebUser.flashcounters|a:0:{}
\.


--
-- Data for Name: system; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY system (id, backup, timezone) FROM stdin;
1	2012-11-21 17:55:00.535688	Europe/Zurich
\.


--
-- Data for Name: target_check_attachments; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_attachments (target_id, check_id, name, type, path, size) FROM stdin;
2	27	_eng_images_support_down_photo_m_ek (1).jpg	image/jpeg	fd3c6970b8053745020efceb15883a50147e657969a961540ab08ce18e564b7d	272561
\.


--
-- Data for Name: target_check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_categories (target_id, check_category_id, advanced, check_count, finished_count, low_risk_count, med_risk_count, high_risk_count, info_count) FROM stdin;
6	6	t	18	3	0	1	0	2
1	1	t	19	15	0	3	0	0
1	2	t	1	1	0	0	0	1
2	3	t	4	0	0	0	0	0
4	1	t	19	0	0	0	0	0
5	1	t	19	0	0	0	0	0
1	8	t	0	0	0	0	0	0
1	11	t	1	1	0	0	0	0
1	3	t	4	0	0	0	0	0
1	4	t	1	0	0	0	0	0
1	5	t	3	0	0	0	0	0
1	10	t	0	0	0	0	0	0
1	6	t	18	2	0	0	0	0
1	9	t	2	2	0	0	0	1
2	6	t	18	5	0	2	0	2
7	1	t	19	2	0	0	0	0
\.


--
-- Data for Name: target_check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_inputs (target_id, check_input_id, value, file, check_id) FROM stdin;
1	1	asdfasdf	\N	1
1	5	120	\N	5
1	6	1	\N	5
1	15	\N	\N	17
1	16	\N	\N	17
1	7	0	\N	6
2	31	10	\N	26
2	32	php	\N	32
1	42	google.com	\N	45
1	46	1	ca9b973efccbd438b561c0bbe293d5d8aa65bac36d2462c8d9112dccdc7175f1	41
1	47	0	fa650a3dca3d2e54caaf729e55f2a7a278db85450b48c212cd011b17f4123c3c	41
1	53	sdfsdf	112eabfee5d18c154167958897b0a669aa17c372b654820615c839a0ff5b5531	46
1	48	0	1b2ff1f4be468757d37653c8a8d783a1718ebcf85853206314062b1f9a387a47	46
1	49	0	0e20fedc2ebaa186018a65c6f99f9e0c2825e604e428bf3fb628c790d61648c3	46
1	12	\N	\N	13
1	50	0	772449b5dc286ddad9d278b69b4c43ee3b321e9af9d1e759295bcea59a27903b	46
1	51	0	e1a03ebbc1b7a80cfe03f4039c896abd4c2a24428301a29eeb17466c66897ec1	46
1	52	0	26a42fe3d37cfb7a78f2829b02c65fb0dc6a46b7bc4b6dc275ab86a1cc2595c2	46
1	54	0	85f14afb07ca1856394103c673e29e1d85c6978ba8e23d66dcf5d71e25d3addb	46
1	55	0	da8d32fc4a97e555126d07344c0de56bbdf3748235c76652d3488cccbe146f2d	46
1	56	0	81f97fda1f738f66f3d358b5fd240b40de926ef8f9357932a6e20b32cb1aee9d	46
1	57	0	fa00f703f15692c2bfbdf8aee7f8b1ec550ce9b237205c3873669492508c13c3	46
1	58	0	26e9992405cf4af800ed2414479ed87bb454feed917b42378d7d836321b8bd68	46
1	11	\N	ab5cc45403bbbf854133a57dd176be8a79aea45e9b9107d45eb634ac763ea322	12
1	8	10	a93b7ccbb92e169a839b12760bd3ea2df83e9fb57f69629d887962f0136672fc	8
1	9	10	c3aaa0222672bd78ce935654e50172da8374d6a2366d4896cd6e111375b3480a	8
1	10	1	f6d144ffb1ee26991b62293e5c059cc598e5e2c00fdbfc15fee0831e989c8385	8
1	14	0	e2d4e99aedc6467c533ceb9c476dfb64b1767bdc8f86f8ed4cba103d488b4cec	16
1	13	0	77cceff0199091fdf1e9e51e7a0c66602a5e9379db0ac1f19efa623362c983cd	15
7	61	demonstratr.com	9811ab7066da26646064dc8a77e38b1c023099d182d943e4eb5af6c9f33868d3	1
7	1	google.com	f28d2121592cbde5216553809cc915dca30791515ab8935bf245986cb03d8b46	1
\.


--
-- Data for Name: target_check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_solutions (target_id, check_solution_id, check_id) FROM stdin;
\.


--
-- Data for Name: target_check_vulns; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_vulns (target_id, check_id, user_id, deadline, status) FROM stdin;
\.


--
-- Data for Name: target_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_checks (target_id, check_id, result, target_file, rating, started, pid, status, result_file, language_id, protocol, port, override_target, user_id, table_result) FROM stdin;
1	8		b83d3c16886e3985ce1eb4c828ba1251d6488a13bb4d7ecc3be6670273200229	\N	2013-01-24 06:08:38.396468	\N	finished	bba14063fbb51e9345682ebe334b1a4c42d85f8be96ae847dcaee0f0f246cecd	1	\N	\N	google.com	1	<gtta-table><columns><column width="0.5" name="Domain"/><column width="0.5" name="IP"/></columns><row><cell>gosgle.com</cell><cell>54.248.125.234</cell></row><row><cell>googpe.com</cell><cell>69.6.27.100</cell></row><row><cell>googlz.com</cell><cell>204.13.160.107</cell></row><row><cell>koogle.com</cell><cell>208.87.34.15</cell></row><row><cell>koogle.com</cell><cell>74.86.197.160</cell></row><row><cell>grogle.com</cell><cell>141.8.224.106</cell></row><row><cell>goole.com</cell><cell>213.165.70.39</cell></row></gtta-table>
1	5	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	17	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	41	**** running Interesting files and dirs scanner ****\n+ interesting Files And Dirs takes awhile....\n+ interesting File or Dir Found: "/logon.php\r"\n+ Item "/logon.php\r" contains header: "Content-Length: 0" MAYBE a False Positive or is empty!\n+ interesting File or Dir Found: "/backend.php\r"\n+ Item "/backend.php\r" contains header: "Content-Length: 0" MAYBE a False Positive or is empty!\n+ interesting File or Dir Found: "/"\n	6b645ef5542a30fab667de4727ebeee69906af3bac2de527b3689cbd1e24d4dd	\N	2013-02-09 07:20:37.218053	\N	finished	a4664d7394c7d9a5948b7189a0172e319ab95c0e36892980421848a3fa8dcf10	1	\N	\N	demonstratr.com	1	\N
1	10	No output.	\N	\N	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	12	Nameserver                     IP                  SOA Serial      Refresh    Retry      Expire     Minimum\n-----------------------------------------------------------------------------------------------------------\nns3.google.com                 IP 216.239.36.10    SOA 2013015000  2h         30m        14d        5m        \nns4.google.com                 IP 216.239.38.10    SOA 2013015000  2h         30m        14d        5m        \nns1.google.com                 IP 216.239.32.10    SOA 2013015000  2h         30m        14d        5m        \nns2.google.com                 IP 216.239.34.10    SOA 2013015000  2h         30m        14d        5m        \nThe recommended syntax for serial number is YYYYMMDDnn (YYYY=year, MM=month, DD=day, nn=revision number), RFC 1912 (2013015000)\nThe recommended value for minimum TTL is 1 to 5 days, RFC 1912 (5m)\n	ef581d8726c59821f7e4f88bbe8cd78de01f23d24dfb1b132cfd4fce87c2f05e	\N	2013-01-21 02:01:22.216199	\N	finished	86a074c1f106e46b6a33cc55934da18d190ad034fa93254db0bd63593e705f8a	1	\N	\N	\N	1	\N
1	13	No output.	5f60a27e9c2111670b7ac522d67ea4a28ea0f82830f54082b96b699dfe045aa4	\N	\N	\N	finished	3bff8cdab5a34d561ea20db0b51d64c820448d8d00dac24f0fab2fbe657acbe8	1	\N	\N	\N	1	\N
1	15		cf3067eb8f2c87ca32d5dcb8f99d51a3bd1241c63b4d60ea9d273e66d8412f1d	\N	2013-01-24 06:15:02.631314	\N	finished	aa3754015e2174188254e999ebde44af5ad7304677cb66d8f4231e0b90394206	1	\N	\N	netprotect.ch	1	<gtta-table><columns><column width="0.5" name="Domain"/><column width="0.5" name="IP"/></columns><row><cell>dev.netprotect.ch</cell><cell>81.6.58.118</cell></row><row><cell>Wildcard DNS: 15 hostname(s)</cell><cell>213.239.210.108</cell></row></gtta-table>
1	16		39e7de7a2aad466f032a8a2f65d894c712d9f1b66651983c9969e44d3d26fa63	\N	2013-01-24 06:11:06.556805	\N	finished	82ebe7c26d634ad9f23b5ae5ecb12a4f8f80c178e633601916c2b51735860bb2	1	\N	\N	netprotect.ch	1	<gtta-table><columns><column width="0.3" name="Domain"/><column width="0.2" name="IP"/><column width="0.2" name="Whois"/><column width="0.3" name="Title"/></columns><row><cell>netprotect.com</cell><cell>54.243.62.158</cell><cell>NETPROTECT.COM</cell><cell>Unblock Us - smarter faster VPN</cell></row><row><cell>netprotect.info</cell><cell>82.98.86.173</cell><cell>na</cell><cell>netprotect.info - ÑÑÐ¾ Ð½Ð°Ð¸Ð»ÑÑÑÐ¸Ð¹ Ð¸ÑÑÐ¾ÑÐ½Ð¸Ðº Ð¸Ð½ÑÐ¾ÑÐ¼Ð°ÑÐ¸Ð¸ Ð¿Ð¾ ÑÐµÐ¼Ðµ netprotect. Ð­ÑÐ¾Ñ Ð²ÐµÐ±-ÑÐ°Ð¹Ñ Ð¿ÑÐ¾Ð´Ð°ÐµÑÑÑ</cell></row><row><cell>netprotect.net</cell><cell>208.91.197.54</cell><cell>NETPROTECT.NET</cell><cell>Loading...</cell></row><row><cell>netprotect.org</cell><cell>64.95.64.218</cell><cell>Buydomains.com</cell><cell>N/A</cell></row><row><cell>netprotect.ws</cell><cell>64.70.19.198</cell><cell>N/A</cell><cell>Find what you are looking for...</cell></row></gtta-table>
1	6	\N	\N	\N	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	45	No output.	\N	\N	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	14	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	3	No output.	\N	\N	\N	\N	finished	\N	1	\N	\N	\N	1	\N
1	11	query failed: NXDOMAIN	ba958ad7b51889ad6b8cfc6c06d9d334417e50010bafe54f2d3df41eb8bd7524	\N	2012-10-12 03:19:08.402282	\N	finished	7b0b9efbc239fb0be3e01c8c7409a51e49797f209f914961c7002a585541ddeb	1	\N	\N	fuck it all	1	\N
1	7	Internal server error. Please send this error code to the administrator - A783BAE2332FE839.	8db8e350872d7f8f5a904757929873d40b4907527b806750727e9acb5fb0ae31	\N	\N	\N	finished	9efe9da38c259364f97fa07233a34a5bb43ae7a83ff2777ec8d021c8b72a40e5	1	\N	\N	80.248.198.9 - 80.248.198.14	1	\N
1	9	Internal server error. Please send this error code to the administrator - 4500C4EA45249D2B.	2a29974663f07789e79bb70d5eeb12a174d8c3618de10bc50182f977e2f5c82b	\N	\N	\N	finished	6f1536eb2f83fd2dd9ca4c1a00424ce2af483b1ab4fbcc632125fae9a4128e7d	1	\N	\N	80.248.198.9	1	\N
2	26	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1	\N
2	28	\N	\N	hidden	\N	\N	finished	\N	1	\N	\N	\N	1	\N
2	29	\N	\N	info	\N	\N	finished	\N	1	http	\N	\N	1	\N
6	28	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1	\N
6	29	\N	\N	info	\N	\N	finished	\N	1	http	\N	\N	1	\N
2	27	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1	\N
6	30	\N	\N	med_risk	\N	\N	finished	\N	1	http	\N	\N	1	\N
1	46	TypeError: main() takes exactly 1 argument (12 given)\n	56ee3e7ede0b52eb03a4032f1816fc25f5194777d91e1f21b623aed6ab03ac5b	\N	2013-05-07 00:05:51.479232	\N	finished	deac86f1e15c8a5ccdfd693cb1e1167c3ecfb5f8d01ffb92fc4dd5b7a9a4f5f6	1	\N	\N	netprotect.ch	1	\N
1	47	No output.	daff9c357f092f45dd347a9ceb199488924a8148820d568eaede13a94728f2a1	\N	2012-11-26 07:09:01.609125	\N	finished	f08845086cba4dc36b7eaccd7071742540942bef7383300185ccb93211904cc6	1	http	80	infoguard.com	1	\N
1	48	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1	\N
2	32	\N	\N	med_risk	\N	\N	finished	\N	1	http	\N	\N	1	\N
1	27	tried 879 time(s) with 0 successful time(s)\n	d1ed11b5e9259aa95e347e6cfb242c6f29264193deff6dca98e7fd06e1ec0ca4	\N	2013-01-21 01:52:55.779798	\N	finished	30a05cb7ef48b856ec5256e9ee09e294c7a9fe0776f94879bc71ee50c2a2be63	1	\N	\N	onexchanger.com	1	\N
1	1	kkkk	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1	<gtta-table>\n    <columns>\n        <column name="Value" width="0.3"/>\n        <column name="Name" width="0.3"/>\n        <column name="Data" width="0.4"/>\n    </columns>\n    <row>\n        <cell>1</cell>\n        <cell>1</cell>\n        <cell>1</cell>\n    </row>\n    <row>\n        <cell>1</cell>\n        <cell>1</cell>\n        <cell>1</cell>\n    </row>\n    <row>\n        <cell>1</cell>\n        <cell>1</cell>\n        <cell>1</cell>\n    </row>\n</gtta-table>
7	3	DNS Servers for demonstratr.com:\n\tdns5.registrar-servers.com\n\tdns1.registrar-servers.com\n\tdns2.registrar-servers.com\n\tdns3.registrar-servers.com\n\tdns4.registrar-servers.com\n\tTesting dns5.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns1.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns2.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns3.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns4.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n	acd1a6ae78a46ad070f4f2be1a97e0ae12c86e74869e88f7b9fcdf80563005f5	\N	2013-05-10 14:38:09.774509	\N	finished	ab7f25ec81dd9dcf0c7810262144b4f3ab43b67b03b5978ea7ce556711d5e292	1	\N	\N	\N	1	\N
7	1	dns_a.py\n--------\n78.46.202.166\n\ndns_a_nr.py\n-----------\ngoogle.com:\nHost not found.\n\n\ndns_a.py\n--------\n78.46.202.166\n\ndns_a_nr.py\n-----------\ngoogle.com:\nHost not found.\n\n\ndns_a.py\n--------\n78.46.202.166\n\ndns_a_nr.py\n-----------\ndemonstratr.com:\nHost not found.\n\n\ndns_a.py\n--------\n78.46.202.166\n\ndns_a_nr.py\n-----------\ndemonstratr.com:\nHost not found.\n\n\ndns_a.py\n--------\n78.46.202.166\n\ndns_a_nr.py\n-----------\ndemonstratr.com:\nHost not found.\n\n	0e8c94fca313041233e461e80954de4b97c930a04920645815fcc2e99b6807d6	\N	2013-05-21 05:12:44.976413	\N	finished	b38d1aed4ff7e26fdd6686df2d9dc101469a275f223bb3946422b97cce861aa4	1	\N	\N	\N	1	\N
\.


--
-- Data for Name: target_references; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_references (target_id, reference_id) FROM stdin;
1	1
2	1
3	1
4	1
5	1
6	1
7	1
\.


--
-- Data for Name: targets; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY targets (id, project_id, host, description) FROM stdin;
3	1	empty.com	\N
4	2	127.0.0.1	\N
5	12	127.0.0.1	\N
2	1	test.com	\N
6	6	test.com	\N
1	1	google.com	Main Webserver
7	1	demonstratr.com	
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY users (id, email, password, name, client_id, role, last_action_time, send_notifications, password_reset_code, password_reset_time, show_reports, show_details, certificate_required, certificate_serial, certificate_issuer) FROM stdin;
4	bob@bob.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3	Anton Belousov	\N	user	2013-05-04 18:23:59.902642	f	\N	\N	f	f	f	\N	\N
3	erbol@gmail.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		2	client	2013-05-13 15:54:42.63288	f	abd9aeef114d88f28ac0ad83fecb70c25a7e22500872eab947ade90244889ee9	\N	t	t	t	FDC71CACAFD354F3	/C=CH/ST=Zurich/L=Zurich/O=GTTA/CN=GTTA
1	erbol.turburgaev@gmail.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3	Oliver Muenchow	\N	admin	2013-06-02 19:30:50	t	\N	2013-05-24 16:02:29.309728	f	f	f	FDC71CACAFD354F2	/C=CH/ST=Zurich/L=Zurich/O=GTTA/CN=GTTA
\.


--
-- Name: check_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_pkey PRIMARY KEY (check_category_id, language_id);


--
-- Name: check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories
    ADD CONSTRAINT check_categories_pkey PRIMARY KEY (id);


--
-- Name: check_controls_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_pkey PRIMARY KEY (check_control_id, language_id);


--
-- Name: check_controls_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_controls
    ADD CONSTRAINT check_controls_pkey PRIMARY KEY (id);


--
-- Name: check_inputs_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_pkey PRIMARY KEY (check_input_id, language_id);


--
-- Name: check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_pkey PRIMARY KEY (id);


--
-- Name: check_results_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_pkey PRIMARY KEY (check_result_id, language_id);


--
-- Name: check_results_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_pkey PRIMARY KEY (id);


--
-- Name: check_scripts_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_scripts
    ADD CONSTRAINT check_scripts_pkey PRIMARY KEY (id);


--
-- Name: check_solutions_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_pkey PRIMARY KEY (check_solution_id, language_id);


--
-- Name: check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_pkey PRIMARY KEY (id);


--
-- Name: checks_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_pkey PRIMARY KEY (check_id, language_id);


--
-- Name: checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_pkey PRIMARY KEY (id);


--
-- Name: clients_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- Name: emails_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_pkey PRIMARY KEY (id);


--
-- Name: gt_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY gt_categories_l10n
    ADD CONSTRAINT gt_categories_l10n_pkey PRIMARY KEY (gt_category_id, language_id);


--
-- Name: gt_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY gt_categories
    ADD CONSTRAINT gt_categories_pkey PRIMARY KEY (id);


--
-- Name: gt_check_dependencies_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY gt_check_dependencies
    ADD CONSTRAINT gt_check_dependencies_pkey PRIMARY KEY (id);


--
-- Name: gt_dependency_processors_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY gt_dependency_processors
    ADD CONSTRAINT gt_dependency_processors_pkey PRIMARY KEY (id);


--
-- Name: gt_module_checks_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY gt_checks_l10n
    ADD CONSTRAINT gt_module_checks_l10n_pkey PRIMARY KEY (gt_check_id, language_id);


--
-- Name: gt_module_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY gt_checks
    ADD CONSTRAINT gt_module_checks_pkey PRIMARY KEY (id);


--
-- Name: gt_modules_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY gt_modules_l10n
    ADD CONSTRAINT gt_modules_l10n_pkey PRIMARY KEY (gt_module_id, language_id);


--
-- Name: gt_modules_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY gt_modules
    ADD CONSTRAINT gt_modules_pkey PRIMARY KEY (id);


--
-- Name: gt_types_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY gt_types_l10n
    ADD CONSTRAINT gt_types_l10n_pkey PRIMARY KEY (gt_type_id, language_id);


--
-- Name: gt_types_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY gt_types
    ADD CONSTRAINT gt_types_pkey PRIMARY KEY (id);


--
-- Name: languages_code_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_code_key UNIQUE (code);


--
-- Name: languages_name_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_name_key UNIQUE (name);


--
-- Name: languages_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- Name: login_history_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY login_history
    ADD CONSTRAINT login_history_pkey PRIMARY KEY (id);


--
-- Name: project_details_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_pkey PRIMARY KEY (id);


--
-- Name: project_gt_check_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_gt_check_attachments
    ADD CONSTRAINT project_gt_check_attachments_pkey PRIMARY KEY (path);


--
-- Name: project_gt_check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_gt_check_inputs
    ADD CONSTRAINT project_gt_check_inputs_pkey PRIMARY KEY (project_id, gt_check_id, check_input_id);


--
-- Name: project_gt_check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_gt_check_solutions
    ADD CONSTRAINT project_gt_check_solutions_pkey PRIMARY KEY (project_id, gt_check_id, check_solution_id);


--
-- Name: project_gt_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_gt_checks
    ADD CONSTRAINT project_gt_checks_pkey PRIMARY KEY (project_id, gt_check_id);


--
-- Name: project_gt_modules_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_gt_modules
    ADD CONSTRAINT project_gt_modules_pkey PRIMARY KEY (project_id, gt_module_id);


--
-- Name: project_gt_suggested_targets_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_gt_suggested_targets
    ADD CONSTRAINT project_gt_suggested_targets_pkey PRIMARY KEY (id);


--
-- Name: project_users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_users
    ADD CONSTRAINT project_users_pkey PRIMARY KEY (project_id, user_id);


--
-- Name: projects_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- Name: references_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY "references"
    ADD CONSTRAINT references_pkey PRIMARY KEY (id);


--
-- Name: report_template_sections_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_sections_l10n
    ADD CONSTRAINT report_template_sections_l10n_pkey PRIMARY KEY (report_template_section_id, language_id);


--
-- Name: report_template_sections_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_pkey PRIMARY KEY (id);


--
-- Name: report_template_sections_report_template_id_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_report_template_id_key UNIQUE (report_template_id, check_category_id);


--
-- Name: report_template_summary_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_summary_l10n
    ADD CONSTRAINT report_template_summary_l10n_pkey PRIMARY KEY (report_template_summary_id, language_id);


--
-- Name: report_template_summary_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_summary
    ADD CONSTRAINT report_template_summary_pkey PRIMARY KEY (id);


--
-- Name: report_templates_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_templates_l10n
    ADD CONSTRAINT report_templates_l10n_pkey PRIMARY KEY (report_template_id, language_id);


--
-- Name: report_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_templates
    ADD CONSTRAINT report_templates_pkey PRIMARY KEY (id);


--
-- Name: risk_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_pkey PRIMARY KEY (risk_category_id, language_id);


--
-- Name: risk_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_categories
    ADD CONSTRAINT risk_categories_pkey PRIMARY KEY (id);


--
-- Name: risk_category_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_category_checks
    ADD CONSTRAINT risk_category_checks_pkey PRIMARY KEY (risk_category_id, check_id);


--
-- Name: risk_templates_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_templates_l10n
    ADD CONSTRAINT risk_templates_l10n_pkey PRIMARY KEY (risk_template_id, language_id);


--
-- Name: risk_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_templates
    ADD CONSTRAINT risk_templates_pkey PRIMARY KEY (id);


--
-- Name: sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: system_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY system
    ADD CONSTRAINT system_pkey PRIMARY KEY (id);


--
-- Name: target_check_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_pkey PRIMARY KEY (path);


--
-- Name: target_check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_pkey PRIMARY KEY (target_id, check_category_id);


--
-- Name: target_check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_pkey PRIMARY KEY (target_id, check_input_id);


--
-- Name: target_check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_pkey PRIMARY KEY (target_id, check_solution_id);


--
-- Name: target_check_vulns_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_pkey PRIMARY KEY (target_id, check_id);


--
-- Name: target_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_pkey PRIMARY KEY (target_id, check_id);


--
-- Name: target_references_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_pkey PRIMARY KEY (target_id, reference_id);


--
-- Name: targets_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_pkey PRIMARY KEY (id);


--
-- Name: users_email_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: check_inputs_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX check_inputs_id_key ON check_inputs USING btree (id);


--
-- Name: check_scripts_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX check_scripts_id_key ON check_scripts USING btree (id);


--
-- Name: check_solutions_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX check_solutions_id_key ON check_solutions USING btree (id);


--
-- Name: checks_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX checks_id_key ON checks USING btree (id);


--
-- Name: gt_categories_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX gt_categories_id_key ON gt_categories USING btree (id);


--
-- Name: gt_dependency_processors_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX gt_dependency_processors_id_key ON gt_dependency_processors USING btree (id);


--
-- Name: gt_module_checks_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX gt_module_checks_id_key ON gt_checks USING btree (id);


--
-- Name: gt_modules_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX gt_modules_id_key ON gt_modules USING btree (id);


--
-- Name: gt_types_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX gt_types_id_key ON gt_types USING btree (id);


--
-- Name: languages_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX languages_id_key ON languages USING btree (id);


--
-- Name: projects_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX projects_id_key ON projects USING btree (id);


--
-- Name: users_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX users_id_key ON users USING btree (id);


--
-- Name: check_categories_l10n_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_controls_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls
    ADD CONSTRAINT check_controls_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_controls_l10n_check_control_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_check_control_id_fkey FOREIGN KEY (check_control_id) REFERENCES check_controls(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_controls_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_inputs_check_script_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_check_script_id_fkey FOREIGN KEY (check_script_id) REFERENCES check_scripts(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_inputs_l10n_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_inputs_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_results_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_results_l10n_check_result_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_check_result_id_fkey FOREIGN KEY (check_result_id) REFERENCES check_results(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_results_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_scripts_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_scripts
    ADD CONSTRAINT check_scripts_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_solutions_l10n_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: check_solutions_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: checks_check_control_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_check_control_id_fkey FOREIGN KEY (check_control_id) REFERENCES check_controls(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: checks_l10n_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: checks_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: checks_reference_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_reference_id_fkey FOREIGN KEY (reference_id) REFERENCES "references"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: emails_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_categories_l10n_gt_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_categories_l10n
    ADD CONSTRAINT gt_categories_l10n_gt_category_id_fkey FOREIGN KEY (gt_category_id) REFERENCES gt_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_categories_l10n
    ADD CONSTRAINT gt_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_check_dependencies_gt_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_check_dependencies
    ADD CONSTRAINT gt_check_dependencies_gt_check_id_fkey FOREIGN KEY (gt_check_id) REFERENCES gt_checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_check_dependencies_gt_module_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_check_dependencies
    ADD CONSTRAINT gt_check_dependencies_gt_module_id_fkey FOREIGN KEY (gt_module_id) REFERENCES gt_modules(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_checks
    ADD CONSTRAINT gt_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_checks_gt_dependency_processor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_checks
    ADD CONSTRAINT gt_checks_gt_dependency_processor_id_fkey FOREIGN KEY (gt_dependency_processor_id) REFERENCES gt_dependency_processors(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_checks_gt_module_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_checks
    ADD CONSTRAINT gt_checks_gt_module_id_fkey FOREIGN KEY (gt_module_id) REFERENCES gt_modules(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_checks_l10n_gt_module_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_checks_l10n
    ADD CONSTRAINT gt_checks_l10n_gt_module_check_id_fkey FOREIGN KEY (gt_check_id) REFERENCES gt_checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_checks_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_checks_l10n
    ADD CONSTRAINT gt_checks_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_modules_gt_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_modules
    ADD CONSTRAINT gt_modules_gt_type_id_fkey FOREIGN KEY (gt_type_id) REFERENCES gt_types(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_modules_l10n_gt_module_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_modules_l10n
    ADD CONSTRAINT gt_modules_l10n_gt_module_id_fkey FOREIGN KEY (gt_module_id) REFERENCES gt_modules(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_modules_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_modules_l10n
    ADD CONSTRAINT gt_modules_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_types_gt_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_types
    ADD CONSTRAINT gt_types_gt_category_id_fkey FOREIGN KEY (gt_category_id) REFERENCES gt_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_types_l10n_gt_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_types_l10n
    ADD CONSTRAINT gt_types_l10n_gt_type_id_fkey FOREIGN KEY (gt_type_id) REFERENCES gt_types(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gt_types_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY gt_types_l10n
    ADD CONSTRAINT gt_types_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: login_history_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY login_history
    ADD CONSTRAINT login_history_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: project_details_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_check_attachments_gt_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_check_attachments
    ADD CONSTRAINT project_gt_check_attachments_gt_check_id_fkey FOREIGN KEY (gt_check_id) REFERENCES gt_checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_check_attachments_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_check_attachments
    ADD CONSTRAINT project_gt_check_attachments_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_check_inputs_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_check_inputs
    ADD CONSTRAINT project_gt_check_inputs_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_check_inputs_gt_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_check_inputs
    ADD CONSTRAINT project_gt_check_inputs_gt_check_id_fkey FOREIGN KEY (gt_check_id) REFERENCES gt_checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_check_inputs_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_check_inputs
    ADD CONSTRAINT project_gt_check_inputs_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_check_solutions_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_check_solutions
    ADD CONSTRAINT project_gt_check_solutions_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_check_solutions_gt_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_check_solutions
    ADD CONSTRAINT project_gt_check_solutions_gt_check_id_fkey FOREIGN KEY (gt_check_id) REFERENCES gt_checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_check_solutions_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_check_solutions
    ADD CONSTRAINT project_gt_check_solutions_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_checks_gt_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_checks
    ADD CONSTRAINT project_gt_checks_gt_check_id_fkey FOREIGN KEY (gt_check_id) REFERENCES gt_checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_checks_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_checks
    ADD CONSTRAINT project_gt_checks_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_checks_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_checks
    ADD CONSTRAINT project_gt_checks_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_checks_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_checks
    ADD CONSTRAINT project_gt_checks_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_modules_gt_module_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_modules
    ADD CONSTRAINT project_gt_modules_gt_module_id_fkey FOREIGN KEY (gt_module_id) REFERENCES gt_modules(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_modules_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_modules
    ADD CONSTRAINT project_gt_modules_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_suggested_targets_gt_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_suggested_targets
    ADD CONSTRAINT project_gt_suggested_targets_gt_check_id_fkey FOREIGN KEY (gt_check_id) REFERENCES gt_checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_suggested_targets_gt_module_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_suggested_targets
    ADD CONSTRAINT project_gt_suggested_targets_gt_module_id_fkey FOREIGN KEY (gt_module_id) REFERENCES gt_modules(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_suggested_targets_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_suggested_targets
    ADD CONSTRAINT project_gt_suggested_targets_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_users_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_users
    ADD CONSTRAINT project_users_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_users_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_users
    ADD CONSTRAINT project_users_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: projects_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_sections_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_sections_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections_l10n
    ADD CONSTRAINT report_template_sections_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_sections_l10n_report_template_section_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections_l10n
    ADD CONSTRAINT report_template_sections_l10n_report_template_section_id_fkey FOREIGN KEY (report_template_section_id) REFERENCES report_template_sections(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_sections_report_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_report_template_id_fkey FOREIGN KEY (report_template_id) REFERENCES report_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_summary_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary_l10n
    ADD CONSTRAINT report_template_summary_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_summary_l10n_report_template_summary_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary_l10n
    ADD CONSTRAINT report_template_summary_l10n_report_template_summary_id_fkey FOREIGN KEY (report_template_summary_id) REFERENCES report_template_summary(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_template_summary_report_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary
    ADD CONSTRAINT report_template_summary_report_template_id_fkey FOREIGN KEY (report_template_id) REFERENCES report_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_templates_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_templates_l10n
    ADD CONSTRAINT report_templates_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: report_templates_l10n_report_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_templates_l10n
    ADD CONSTRAINT report_templates_l10n_report_template_id_fkey FOREIGN KEY (report_template_id) REFERENCES report_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_categories_l10n_risk_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_risk_category_id_fkey FOREIGN KEY (risk_category_id) REFERENCES risk_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_categories_risk_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories
    ADD CONSTRAINT risk_categories_risk_template_id_fkey FOREIGN KEY (risk_template_id) REFERENCES risk_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_category_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_category_checks
    ADD CONSTRAINT risk_category_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_category_checks_risk_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_category_checks
    ADD CONSTRAINT risk_category_checks_risk_category_id_fkey FOREIGN KEY (risk_category_id) REFERENCES risk_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_templates_l10n_risk_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_templates_l10n
    ADD CONSTRAINT risk_templates_l10n_risk_template_id_fkey FOREIGN KEY (risk_template_id) REFERENCES risk_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: risk_templatess_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_templates_l10n
    ADD CONSTRAINT risk_templatess_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_attachments_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_attachments_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_attachments_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_categories_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_categories_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_inputs_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_inputs_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_inputs_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_solutions_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_solutions_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_solutions_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_vulns_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_vulns_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_vulns_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_check_vulns_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_checks_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_checks_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_checks_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_references_reference_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_reference_id_fkey FOREIGN KEY (reference_id) REFERENCES "references"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: target_references_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: targets_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

