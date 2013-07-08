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

SELECT pg_catalog.setval('check_categories_id_seq', 12, true);


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

SELECT pg_catalog.setval('check_controls_id_seq', 18, true);


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

SELECT pg_catalog.setval('check_inputs_id_seq', 74, true);


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

SELECT pg_catalog.setval('check_scripts_id_seq', 49, true);


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

SELECT pg_catalog.setval('checks_id_seq', 57, true);


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

SELECT pg_catalog.setval('emails_id_seq', 25, true);


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

SELECT pg_catalog.setval('gt_categories_id_seq', 6, true);


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

SELECT pg_catalog.setval('gt_check_dependencies_id_seq', 6, true);


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

SELECT pg_catalog.setval('gt_checks_id_seq', 14, true);


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

SELECT pg_catalog.setval('gt_modules_id_seq', 8, true);


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

SELECT pg_catalog.setval('gt_types_id_seq', 11, true);


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

SELECT pg_catalog.setval('login_history_id_seq', 150, true);


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
-- Name: project_gt_check_vulns; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_gt_check_vulns (
    project_id bigint NOT NULL,
    gt_check_id bigint NOT NULL,
    user_id bigint,
    deadline date,
    status vuln_status DEFAULT 'open'::vuln_status NOT NULL
);


ALTER TABLE public.project_gt_check_vulns OWNER TO gtta;

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

SELECT pg_catalog.setval('project_gt_suggested_targets_id_seq', 31, true);


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

SELECT pg_catalog.setval('projects_id_seq', 14, true);


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
6	Web Anonymous
1	DNS
8	Eine Kleine
10	zed
9	AUTHENTICATED WEB CHECKS
11	New & Modified Checks
12	pzasdfasdf
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
12	1	pzasdfasdf
12	2	\N
\.


--
-- Data for Name: check_controls; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_controls (id, check_category_id, name, sort_order) FROM stdin;
2	2	Default	2
3	3	Default	3
4	4	Default	4
6	6	Default	6
7	1	This is a long name of the control	7
9	1	Some other important stuff	8
11	1	Empty Control	9
8	1	Session Handling	13
1	1	Default	1
12	11	New checks	12
13	1	1	11
14	1	2	14
15	1	3	15
16	1	4	16
17	1	5	17
18	1	kdsjflkajsdfl	18
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
18	1	kdsjflkajsdfl
18	2	\N
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
32	Code	Possible values: php, cfm, asp.	0	php	0	23
34	Paths		0		0	26
33	URLs		0		0	25
35	Paths		0		0	27
36	Paths		0		0	28
6	Debug		1	1	0	35
46	Admin Logins		0		4	32
38	Page Type	Possible values: php, asp.	0	php	0	34
39	Cookies		1		0	34
40	URL Limit		2	100	0	34
42	Hostname		0		0	41
62	Hostname X		0	asdfasdf	0	46
63	Hostname		0		0	47
64	Show All		0	0	0	48
69	Ports	Nmap ports.	0		0	49
70	Skip Discovery	Skip host discovery.	1	1	2	49
71	Verbose	Verbose output.	2	1	2	49
72	Probe	Probe open ports to determine software info.	3	1	2	49
73	Timing		4	2	0	49
74	Extract	Extract data from nmap output. THIS OPTION IS REQUIRED FOR CHECK DEPENDENCIES IN GUIDED TEST CHECKS.	5	1	2	49
\.


--
-- Data for Name: check_inputs_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs_l10n (check_input_id, language_id, name, description) FROM stdin;
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
38	1	Page Type	Possible values: php, asp.
38	2	\N	\N
39	1	Cookie	\N
39	2	\N	\N
40	1	URL Limit	\N
40	2	\N	\N
42	1	Hostname	\N
42	2	\N	\N
69	1	Ports	Nmap ports.
69	2	\N	\N
62	1	Hostname X	\N
62	2	\N	\N
70	1	Skip Discovery	Skip host discovery.
70	2	\N	\N
71	1	Verbose	Verbose output.
71	2	\N	\N
72	1	Probe	Probe open ports to determine software info.
72	2	\N	\N
46	1	Admin Logins	\N
46	2	\N	\N
63	1	Hostname	\N
63	2	\N	\N
73	1	Timing	\N
73	2	\N	\N
47	1	Adobe xml	\N
47	2	\N	\N
74	1	Extract	Extract data from nmap output. THIS OPTION IS REQUIRED FOR CHECK DEPENDENCIES IN GUIDED TEST CHECKS.
74	2	\N	\N
64	1	Show All	\N
64	2	\N	\N
\.


--
-- Data for Name: check_results; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results (id, check_id, result, sort_order, title) FROM stdin;
3	3	Resulten	1	Test Deutsche
2	3	Here is no formatting at all - because this field is plain text. Please humble with that.\r\n\r\nLine span.	0	Test English
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
34	43	web_sql_xss.py
35	5	dns_find_ns.pl
36	8	nic_typosquatting.pl
37	11	dns_resolve_ip.pl
38	14	dns_spf.pl
39	47	cms_detection.py
40	3	dns_afxr.pl
41	45	dns_a_nr.py
43	6	dns_hosting.py
46	55	dns_a.py
47	55	dns_a_nr.py
48	56	dns_hosting.py
42	1	params_crawler.py
33	42	renegotiation.py
17	26	ssl_quality.py
49	57	nmap_tcp.pl
\.


--
-- Data for Name: check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions (id, check_id, solution, sort_order, title) FROM stdin;
5	3	i love you too	1	tears of prophecy
4	3	<i>zoo</i><br><i>gooooo<br><br></i>pom pom<br><i><br></i><b>black</b>	0	aduljadei
\.


--
-- Data for Name: check_solutions_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions_l10n (check_solution_id, language_id, solution, title) FROM stdin;
5	1	i love you too	tears of prophecy
5	2	ich liebe dir	\N
4	1	<i>zoo</i><br><i>gooooo<br><br></i>pom pom<br><i><br></i><b>black</b>	aduljadei
4	2	\N	\N
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
56	1	DNS Hosting (Copy)	hello		f	t	f	adfasd	\N		1			2	56
22	4	SSH Bruteforce			f	t	f		\N		1			2	22
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
47	12	CMS check			f	t	f	http	80		1			2	47
49	13	hh			f	f	f		\N		1			2	49
1	1	DNS A			f	t	f		\N		1			2	0
57	12	Nmap TCP Port Scan			f	t	f		\N		1			2	57
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
50	1	DNS TEST	test	\N	\N	\N
47	1	CMS check	\N	\N	\N	\N
50	2	\N	\N	\N	\N	\N
47	2	\N	\N	\N	\N	\N
49	1	hh	\N	\N	\N	\N
49	2	\N	\N	\N	\N	\N
54	1	DNS A (Copy)	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.	\N	\N	\N
54	2	ZZZ (Copy)	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.	\N	\N	\N
57	1	Nmap TCP Port Scan	\N	\N	\N	\N
57	2	\N	\N	\N	\N	\N
1	1	DNS A	\N	\N	\N	\N
1	2	ZZZ	\N	\N	\N	\N
55	1	DNS A (Copy)	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.	\N	\N	\N
55	2	ZZZ (Copy)	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.	\N	\N	\N
56	1	DNS Hosting (Copy)	hello	\N	\N	\N
56	2	 (Copy)	\N	\N	\N	\N
\.


--
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY clients (id, name, country, state, city, address, postcode, website, contact_name, contact_phone, contact_email, contact_fax, logo_path, logo_type) FROM stdin;
4	Helloy										123-123-123	\N	\N
2	IBM											\N	\N
1	Apple	Switzerland		Zurich	Kallison Lane, 7	123456	http://netprotect.ch	Ivan John		invan@john.com	123-123-123	40453852965cebb2b0dbc5440323bea3f5adf750c8b5f72e06b5fc7a9aad9da4	image/png
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
6	Technical Test
\.


--
-- Data for Name: gt_categories_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_categories_l10n (gt_category_id, language_id, name) FROM stdin;
6	1	Technical Test
6	2	\N
\.


--
-- Data for Name: gt_check_dependencies; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_check_dependencies (id, gt_check_id, gt_module_id, condition) FROM stdin;
6	11	8	22
\.


--
-- Data for Name: gt_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_checks (id, gt_module_id, check_id, description, target_description, sort_order, gt_dependency_processor_id) FROM stdin;
14	8	1	DNS A check.		0	\N
11	7	57	Brief description.		0	1
\.


--
-- Data for Name: gt_checks_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_checks_l10n (gt_check_id, language_id, description, target_description) FROM stdin;
14	1	DNS A check.	Target description.
14	2	\N	\N
11	1	Brief description.	Some target description.
11	2	\N	\N
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
7	11	External Penetration Test
8	11	Internal Penetration Test
\.


--
-- Data for Name: gt_modules_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_modules_l10n (gt_module_id, language_id, name) FROM stdin;
7	1	External Penetration Test
7	2	\N
8	1	Internal Penetration Test
8	2	\N
\.


--
-- Data for Name: gt_types; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_types (id, gt_category_id, name) FROM stdin;
11	6	Network Based
\.


--
-- Data for Name: gt_types_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY gt_types_l10n (gt_type_id, language_id, name) FROM stdin;
11	1	Network Based
11	2	\N
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
131	1	Oliver Muenchow	2013-06-03 03:29:33.473036
132	1	Oliver Muenchow	2013-06-03 04:26:06.533179
133	1	Oliver Muenchow	2013-06-03 20:49:00.199979
134	1	Oliver Muenchow	2013-06-04 01:07:45.964216
135	1	Oliver Muenchow	2013-06-04 01:09:37.374729
136	1	Oliver Muenchow	2013-06-04 02:12:51.064858
137	1	Oliver Muenchow	2013-06-04 02:13:21.284143
138	1	Oliver Muenchow	2013-06-04 02:13:54.52118
139	1	Oliver Muenchow	2013-06-04 02:14:25.133452
140	1	Oliver Muenchow	2013-06-04 02:15:28.758402
141	1	Oliver Muenchow	2013-06-04 02:19:12.991945
142	1	Oliver Muenchow	2013-06-05 03:34:14.334459
143	1	Oliver Muenchow	2013-06-05 14:17:07.992134
144	1	Oliver Muenchow	2013-06-05 23:00:40.67239
145	1	Oliver Muenchow	2013-06-06 01:02:16.593305
146	1	Oliver Muenchow	2013-06-06 15:07:10.591782
147	1	Oliver Muenchow	2013-06-12 16:28:14.475447
148	1	Oliver Muenchow	2013-06-12 19:44:24.027997
149	1	Oliver Muenchow	2013-07-03 16:41:12.210185
150	1	Oliver Muenchow	2013-07-08 16:39:23.212236
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
8	11	69	21,22,80,443	\N
8	11	73	2	\N
8	11	70	1	\N
8	11	71	0	\N
8	11	72	0	\N
8	11	74	1	\N
\.


--
-- Data for Name: project_gt_check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_gt_check_solutions (project_id, gt_check_id, check_solution_id) FROM stdin;
\.


--
-- Data for Name: project_gt_check_vulns; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_gt_check_vulns (project_id, gt_check_id, user_id, deadline, status) FROM stdin;
8	11	1	2013-07-01	open
\.


--
-- Data for Name: project_gt_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_gt_checks (project_id, gt_check_id, user_id, language_id, target, port, protocol, target_file, result_file, result, table_result, started, pid, rating, status) FROM stdin;
8	11	1	1	demonstratr.com	\N	\N	c07fc62f566134d0ea9f0cb82ca7da86a1b710d72828679bbd95702774434439	66d438cb7eeadb1ef3d2bcae1caa3ec1e015d8f2150b5097736f72161c38b99e	No output.\nnmap_tcp.pl\n-----------\n	<gtta-table><columns><column width="0.3" name="Address"/><column width="0.2" name="Port"/><column width="0.2" name="Service"/><column width="0.3" name="Product"/></columns><row><cell>78.46.202.166 (static.166.202.46.78.clients.your-server.de)</cell><cell>21</cell><cell>ftp</cell><cell>N/A</cell></row><row><cell>78.46.202.166 (static.166.202.46.78.clients.your-server.de)</cell><cell>22</cell><cell>ssh</cell><cell>N/A</cell></row><row><cell>78.46.202.166 (static.166.202.46.78.clients.your-server.de)</cell><cell>80</cell><cell>http</cell><cell>N/A</cell></row><row><cell>78.46.202.166 (static.166.202.46.78.clients.your-server.de)</cell><cell>443</cell><cell>https</cell><cell>N/A</cell></row></gtta-table>	2013-06-12 15:50:02	\N	med_risk	finished
8	14	1	1	\N	\N	\N	\N	\N	\N	\N	\N	\N	high_risk	finished
\.


--
-- Data for Name: project_gt_modules; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_gt_modules (project_id, gt_module_id, sort_order) FROM stdin;
8	7	0
8	8	1
\.


--
-- Data for Name: project_gt_suggested_targets; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_gt_suggested_targets (id, project_id, gt_module_id, target, gt_check_id, approved) FROM stdin;
30	8	8	78.46.202.166	11	t
31	8	8	static.166.202.46.78.clients.your-server.de	11	t
\.


--
-- Data for Name: project_users; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_users (project_id, user_id, admin) FROM stdin;
8	4	t
8	1	t
1	1	t
13	1	t
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
11	2	2012	2012-09-21	eee	open	\N	f
5	1	2012	2012-09-21	zzz	finished	\N	f
13	4	2012	2013-02-09	Buka	open	\N	f
2	2	2012	2012-07-29	Blabsdlfbasldfb	finished	\N	f
10	1	2012	2012-09-21	ddd	open	\N	t
8	2	2012	2012-09-21	ccc	in_progress	2013-07-08	t
1	2	2012	2012-07-27	Test	in_progress	2013-07-08	f
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
0b053b774jea27sol232h71k05      	1373299341	1kfyNj5hP6S1bodt-xvQ23ZA5jUQC2ZPwtOv6EvjHCaaALgjyClPIbnJTWyzktgJRDGtVlNS74isM3DDG3QuA2YyEvGCKh7I1yI-XOJYIdELlNx4r4J3zpqkMadFPz9r3Lc1vemWefyiEXuDcF-2v3V-xa4f6w7CiKf-zQuu57-LzD7EctSKpzqddqQg0gHYZ5OPMJkNKZXELlRszFXxJX9fwMgg_R6roVCuHJaufnaNHm0RB8qgB8-rEOII2Kw_TXvEjlcH5bA68FU9jjUhPRXRpZYrsQAH9eqdti_Ds6pXTpQauX0FZmY9Dn0BzEO1qs7Hged_RgVqXAX8Eh4fprN09FYnL38LPPwPvF1k-ZZDiovBtp_pXhZa6EuGlVJHp9mw3npTfBpeTHRKy5iIMY6TfedDShyiwg3i8mIo58WGIzbFH72RM2G9xaccoq-T
\.


--
-- Data for Name: system; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY system (id, backup, timezone) FROM stdin;
1	2013-06-03 02:46:16	Europe/Zurich
\.


--
-- Data for Name: target_check_attachments; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_attachments (target_id, check_id, name, type, path, size) FROM stdin;
2	27	_eng_images_support_down_photo_m_ek (1).jpg	image/jpeg	fd3c6970b8053745020efceb15883a50147e657969a961540ab08ce18e564b7d	272561
7	1	change_business~ipad.png	image/png	4b8980a1d9f7448bf45facd37d17c8e05a525665b756aae9e401ba4d4099c245	11729
\.


--
-- Data for Name: target_check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_categories (target_id, check_category_id, advanced, check_count, finished_count, low_risk_count, med_risk_count, high_risk_count, info_count) FROM stdin;
7	1	t	20	2	0	0	1	0
6	6	t	18	3	0	1	0	2
1	2	t	1	1	0	0	0	1
2	3	t	4	0	0	0	0	0
1	8	t	0	0	0	0	0	0
1	3	t	4	0	0	0	0	0
1	4	t	1	0	0	0	0	0
1	10	t	0	0	0	0	0	0
1	9	t	0	0	0	0	0	0
2	6	t	18	5	0	2	0	2
1	6	t	18	4	0	0	0	0
1	11	t	2	1	0	0	0	0
4	1	t	20	0	0	0	0	0
1	1	t	20	15	0	2	0	0
\.


--
-- Data for Name: target_check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_inputs (target_id, check_input_id, value, file, check_id) FROM stdin;
1	5	120	\N	5
1	6	1	\N	5
1	15	\N	\N	17
1	16	\N	\N	17
1	7	0	\N	6
2	32	php	\N	32
1	42	google.com	\N	45
1	46	1	ca9b973efccbd438b561c0bbe293d5d8aa65bac36d2462c8d9112dccdc7175f1	41
1	47	0	fa650a3dca3d2e54caaf729e55f2a7a278db85450b48c212cd011b17f4123c3c	41
1	12	\N	\N	13
1	8	10	a93b7ccbb92e169a839b12760bd3ea2df83e9fb57f69629d887962f0136672fc	8
1	9	10	c3aaa0222672bd78ce935654e50172da8374d6a2366d4896cd6e111375b3480a	8
1	10	1	f6d144ffb1ee26991b62293e5c059cc598e5e2c00fdbfc15fee0831e989c8385	8
1	14	0	e2d4e99aedc6467c533ceb9c476dfb64b1767bdc8f86f8ed4cba103d488b4cec	16
1	13	0	77cceff0199091fdf1e9e51e7a0c66602a5e9379db0ac1f19efa623362c983cd	15
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
7	1	1	2013-07-01	open
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
1	47	No output.	daff9c357f092f45dd347a9ceb199488924a8148820d568eaede13a94728f2a1	\N	2012-11-26 07:09:01.609125	\N	finished	f08845086cba4dc36b7eaccd7071742540942bef7383300185ccb93211904cc6	1	http	80	infoguard.com	1	\N
2	32	\N	\N	med_risk	\N	\N	finished	\N	1	http	\N	\N	1	\N
1	27	tried 879 time(s) with 0 successful time(s)\n	d1ed11b5e9259aa95e347e6cfb242c6f29264193deff6dca98e7fd06e1ec0ca4	\N	2013-01-21 01:52:55.779798	\N	finished	30a05cb7ef48b856ec5256e9ee09e294c7a9fe0776f94879bc71ee50c2a2be63	1	\N	\N	onexchanger.com	1	\N
7	3	DNS Servers for demonstratr.com:\n\tdns5.registrar-servers.com\n\tdns1.registrar-servers.com\n\tdns2.registrar-servers.com\n\tdns3.registrar-servers.com\n\tdns4.registrar-servers.com\n\tTesting dns5.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns1.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns2.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns3.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns4.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n	acd1a6ae78a46ad070f4f2be1a97e0ae12c86e74869e88f7b9fcdf80563005f5	\N	2013-05-10 14:38:09.774509	\N	finished	ab7f25ec81dd9dcf0c7810262144b4f3ab43b67b03b5978ea7ce556711d5e292	1	\N	\N	\N	1	\N
7	1	dns_a.py\n--------\n78.46.202.166\n\ndns_a_nr.py\n-----------\ngoogle.com:\nHost not found.\n\n\ndns_a.py\n--------\n78.46.202.166\n\ndns_a_nr.py\n-----------\ngoogle.com:\nHost not found.\n\n\ndns_a.py\n--------\n78.46.202.166\n\ndns_a_nr.py\n-----------\ndemonstratr.com:\nHost not found.\n\n\ndns_a.py\n--------\n78.46.202.166\n\ndns_a_nr.py\n-----------\ndemonstratr.com:\nHost not found.\n\n\ndns_a.py\n--------\n78.46.202.166\n\ndns_a_nr.py\n-----------\ndemonstratr.com:\nHost not found.\n\n	0e8c94fca313041233e461e80954de4b97c930a04920645815fcc2e99b6807d6	high_risk	2013-05-21 05:12:44.976413	\N	finished	b38d1aed4ff7e26fdd6686df2d9dc101469a275f223bb3946422b97cce861aa4	1	\N	\N	\N	1	\N
1	1	params_crawler.py\n-----------------\ngaierror: [Errno -2] Name or service not known\n\nparams_crawler.py\n-----------------\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 7243\n\temail_address=\nTypeError: _write_result() takes exactly 2 arguments (1 given)\n\nparams_crawler.py\n-----------------\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 1736\n\temail_address=\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t559d245490884ac575d9599ec04b5ac3=1\n\nhttp://tehnologiyaklimata.com/index.php?format=feed&amp;type=rss\n\ttype=rss\n\tformat=feed\n\nhttp://tehnologiyaklimata.com/index.php?format=feed&amp;type=atom\n\ttype=atom\n\tformat=feed\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\ta46328fa25573bad118650f7e61e052f=1\n\nhttp://tehnologiyaklimata.com/index.php/extensions/s5-quick-contact?format=feed&amp;type=rss\n\ttype=rss\n\tformat=feed\n\nhttp://tehnologiyaklimata.com/index.php/extensions/s5-quick-contact?format=feed&amp;type=atom\n\ttype=atom\n\tformat=feed\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t81485f1bb699e3d0aa7443c3d480ca70=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t4b87952503ae452a26b908700c4285b2=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t69543cc41990808d9fa903b07ece9561=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tdb872b43d415d4c2c9bfbdf790ddc25e=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tf2ed2cca003b3e68e01d6bc7ed8e7e12=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t2e71daed96aef12bd2366ad0d4eb35d8=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t6e004b2c56e979b96d646465314250a9=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\te9110bd5f1019f61a8674ef32df132ea=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tc2452652d5652f4fc761b6f33e68682d=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tc2345a63c77051eeb1329e74519d4547=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t260598a2f956bbcbe3b70751274c7fe1=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tbe35fcb328f7f8f6d3db2005b829ddc4=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tfc97d7a83e495c37cf50245727cd64d4=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t9ab99f33da69f102bc1c5327917548e7=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t4abc3e069efc925f91cb8857508a3043=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t7cb7adcbb67e17e163b583a87c5dba09=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t60e79b8026ca38b3eaa910595b397502=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t31fcf3f1acf47048b0a691c7b5a4583d=1\n\nhttp://tehnologiyaklimata.com/?lang=ltr\n\tlang=ltr\n\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 896\n\temail_address=\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t80624542307f2793bde92b1cb26b9d9b=1\n\nhttp://tehnologiyaklimata.com/?lang=rtl\n\tlang=rtl\n\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 3084\n\temail_address=\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tbcbeb5c21817dc4b317967f2caf12975=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/search-mainmenu-5\n\tsearchword=\n\ttask=search\n\tsearchphrase=all\n\tsearchphrase=any\n\tsearchphrase=exact\n\tordering=\n\tareas[]=categories\n\tareas[]=contacts\n\tareas[]=content\n\tareas[]=newsfeeds\n\tareas[]=weblinks\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tce7709c8e99ed7335a82f5772b8f8a2b=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\ta8d985e46b0a1e5a36d2637080ee7bb8=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?format=feed&amp;type=rss\n\ttype=rss\n\tformat=feed\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?format=feed&amp;type=atom\n\ttype=atom\n\tformat=feed\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t645fdafc2f36c0f6976f83e48b92863c=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\taac23efd31181424bef426d5f5285846=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/contact-us\n\tjform[contact_name]=\n\tjform[contact_email]=\n\tjform[contact_subject]=\n\tjform[contact_email_copy]=\n\toption=com_contact\n\ttask=contact.submit\n\treturn=\n\tid=1:name\n\t02e18d24713250d4f1831a00c2ee2445=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t02e18d24713250d4f1831a00c2ee2445=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tcf55ab5770c9524d7ba24386fc46478a=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t0185a817fcfd12ef95deec25e814b283=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\ta6ce08985ac7a5b50046d076479fe66a=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t6208a06aef178b4c9c6936a301ebe49c=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tcc36681bde992b62fb176c84327dbf93=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\td3ff1652fd680eb2818dadd974a33967=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t5a7787f1aa15c05e97a8da66038c06e8=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\td7b36ffc0fc1fc945b0cdd1ee8edaf4b=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tc5d062ddd27a558ab26bb910eab6ff0b=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\ta5fdf35f4600eb8e3670a176a00f2a67=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tc39f9b6c9867aace114dd144a838abe1=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t8f11263bf5072374aee6c816be6e56c6=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t2481efed6112ab19eab9f3f9fcfb776b=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t83cdc6b0d8789a0f39edc340bf7671eb=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t93a1a21a39d9cb6950f31c911e21444f=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t7f9f2557b3592219a7f748b5e8fa1c56=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t008f8ee9296d3f92864b4cb1e81ba60a=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t0a38e64c316d18d82fea6217065221b6=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tb62c16f7b342de0045fa99b06efb08fd=1\n\nhttp://tehnologiyaklimata.com/index.php/tutorials-mainmenu-48/lazy-load-setup?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=9352227dba61ff02ee68f195640ed172fba546ef\n\ttmpl=component\n\tlink=9352227dba61ff02ee68f195640ed172fba546ef\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=9352227dba61ff02ee68f195640ed172fba546ef\n\t5454980dce69fa511fbff8dc7613c2e5=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t131e5cf98d342b7de03ea506ddf75fcb=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t1a7094a8514302c4d420eedd6bde6449=1\n\nhttp://tehnologiyaklimata.com/index.php/careers-11451?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=bc1e394dd402882154b21a0c5d7e6815098dc57e\n\ttmpl=component\n\tlink=bc1e394dd402882154b21a0c5d7e6815098dc57e\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=bc1e394dd402882154b21a0c5d7e6815098dc57e\n\t5e2560fcafc7446f1299f365190cef44=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t779c5cbf9fd0dc675b0ff701c49641ce=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t5856c30ad4b1ad7027b69615766dba79=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tf486519f772c41f565f240cd79b7474d=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t3794bf992ade8a3df2fe97042e3de1f1=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t870d19e94e29b799083147de4b7b4e20=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\td57da330807f72b68c55944f8949d214=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t19005ddc236b4f3436824b35bd96f911=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\te67b3f46ca10c030ba74d676afdfc6b8=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t405c5544688b69fd852e6a1536aa3b04=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t21371618ce8dd1e4a048de776ccc5aae=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tc4108fd46da75a2b4f16aad74b2ac761=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t6cff12b66e8bf3975311a627e4b5bc6b=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tbc5ec21e8861a98a6dbfa0cf5937ca51=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t40565396c42262c5220f85f8462660c4=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t51793c42100f37bd19c8ad5a39c4115b=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t84f8817398c85152c2373210c031dc94=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t825eba713ea0e0ae52e4d504ba8afc3f=1\n\nhttp://tehnologiyaklimata.com/index.php/tutorials-mainmenu-48/tool-tips-setup?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=a989cbd2dc6171d06a677438e7ec723d9f54aa7d\n\ttmpl=component\n\tlink=a989cbd2dc6171d06a677438e7ec723d9f54aa7d\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=a989cbd2dc6171d06a677438e7ec723d9f54aa7d\n\t0d62d599d454fe58c154f3a44aaeca6b=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\te7aaa5df9f54e4f5b7ad55fb0a245981=1\n\nhttp://tehnologiyaklimata.com/index.php/tutorials-mainmenu-48/multibox-setup?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=83881d59c4a22142c88f3dcbe09b075d03cf526f\n\ttmpl=component\n\tlink=83881d59c4a22142c88f3dcbe09b075d03cf526f\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=83881d59c4a22142c88f3dcbe09b075d03cf526f\n\t0093a5cfc50e7c438a1716606179e738=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t499377fb46c4de92d5b0ec1465407413=1\n\nhttp://tehnologiyaklimata.com/index.php/tutorials-mainmenu-48/s5-login-and-register-setup?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=c68236d96eb7ee6e64a2ae9ed8547adf495303e5\n\ttmpl=component\n\tlink=c68236d96eb7ee6e64a2ae9ed8547adf495303e5\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=c68236d96eb7ee6e64a2ae9ed8547adf495303e5\n\t255573c16876fae65e92ee649d37072e=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tc1e23c6a90d2a22626b2d473900277ae=1\n\nhttp://tehnologiyaklimata.com/index.php/tutorials-mainmenu-48/search-and-menus-setup?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=b345cfc693c214de70897f5fae7fc9677b524371\n\ttmpl=component\n\tlink=b345cfc693c214de70897f5fae7fc9677b524371\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=b345cfc693c214de70897f5fae7fc9677b524371\n\t424e6016c1f731c9ee9e150a1994d668=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t5ddd9c27e92b703b534b06360038d405=1\n\nhttp://tehnologiyaklimata.com/index.php/tutorials-mainmenu-48/configuring-the-template?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=e1ff945d14e73d2778ee74f1f342bcf1d38bd94d\n\ttmpl=component\n\tlink=e1ff945d14e73d2778ee74f1f342bcf1d38bd94d\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=e1ff945d14e73d2778ee74f1f342bcf1d38bd94d\n\t33459b3c5505bff60ab302bbff949854=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t742d149dab813d431e31bfb88bb02db7=1\n\nhttp://tehnologiyaklimata.com/index.php/tutorials-mainmenu-48/setting-up-module-styles?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=f96482a1048b59d333f297a31cbfb4180f6dae2d\n\ttmpl=component\n\tlink=f96482a1048b59d333f297a31cbfb4180f6dae2d\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=f96482a1048b59d333f297a31cbfb4180f6dae2d\n\tf41145f22745ecad4c3ef76b39cdbd40=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t44f6639beafa8a00224b765b4ad0fb68=1\n\nhttp://tehnologiyaklimata.com/index.php/tutorials-mainmenu-48/installing-the-template?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=581da5b398a6e70e6366d3a5f1906ab0bac96ab2\n\ttmpl=component\n\tlink=581da5b398a6e70e6366d3a5f1906ab0bac96ab2\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=581da5b398a6e70e6366d3a5f1906ab0bac96ab2\n\td1c1135cd9f6e573ba681ad212fee395=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t3c54a50648eb2e04496d46c889a3ffc0=1\n\nhttp://tehnologiyaklimata.com/index.php/tutorials-mainmenu-48/site-shaper-setup?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=f65da55b603e9cabb706abb781d74c03384914c5\n\ttmpl=component\n\tlink=f65da55b603e9cabb706abb781d74c03384914c5\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=f65da55b603e9cabb706abb781d74c03384914c5\n\ta8f16e6dc83b2d1df5d07c0646fa66ba=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\te5e12a9e6971f87b535c74203cfc69b1=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/style-and-layout-options/3rd-party-component-compatible?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t43a4589551b877dc9d46ff7a734f25dc=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/style-and-layout-options/3rd-party-component-compatible/22-featured-news?format=feed&amp;type=rss\n\ttype=rss\n\tformat=feed\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/style-and-layout-options/3rd-party-component-compatible/22-featured-news?format=feed&amp;type=atom\n\ttype=atom\n\tformat=feed\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=6de5056663bbc729e449712d27dd36b4d24b7643\n\ttmpl=component\n\tlink=6de5056663bbc729e449712d27dd36b4d24b7643\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=6de5056663bbc729e449712d27dd36b4d24b7643\n\ta1d81a7ac4d789ebf273338aaa5f85b0=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\te40dba0e61e90af745989ca360d0d361=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/style-and-layout-options/3rd-party-component-compatible/22-featured-news/254-s5-quick-contact?tmpl=component&amp;print=1&amp;page=\n\tprint=1\n\ttmpl=component\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=77e00d1d67384ebd7a495ae3caa4f0022e4c5031\n\ttmpl=component\n\tlink=77e00d1d67384ebd7a495ae3caa4f0022e4c5031\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=77e00d1d67384ebd7a495ae3caa4f0022e4c5031\n\t869c04355f969d72c2c0d89a1e361bbb=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t1c7778eb9eb06c053476062c304e28d3=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t31d58193531acfabe0b0e5bcdfbbc704=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tebb75da52a931fc052a585937f393911=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/style-and-layout-options/ie7-and-8-css3-support?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=664e79e6cb8dc414ce47afd539e9dfd12d82793e\n\ttmpl=component\n\tlink=664e79e6cb8dc414ce47afd539e9dfd12d82793e\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=664e79e6cb8dc414ce47afd539e9dfd12d82793e\n\t63dd2edbc2e4282fe0ecf7afc14ca0d8=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t8b0688dbd8b433028d833995082655a1=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/style-and-layout-options/fixed-side-tabs?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=4d0f36c91977a53dcd24ee590fd1aedc683f3684\n\ttmpl=component\n\tlink=4d0f36c91977a53dcd24ee590fd1aedc683f3684\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=4d0f36c91977a53dcd24ee590fd1aedc683f3684\n\tc6ddfe308608f80085f1fe6bb332906e=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tb2f6dbedb7b78cb7b6839a3eb9c23b65=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/style-and-layout-options/fluid-and-fixed-layouts?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=a6906ab69aa111cdc3ae0e3aed2066c7c6ab505f\n\ttmpl=component\n\tlink=a6906ab69aa111cdc3ae0e3aed2066c7c6ab505f\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=a6906ab69aa111cdc3ae0e3aed2066c7c6ab505f\n\tdb3614c29a2ff398ed314b90351ad71e=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t1729170c7e06357991e2654bb1d1c5b1=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/style-and-layout-options/css-tableless-overrides?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=5a0c01671b25beb4458e56a34acec621b037aee2\n\ttmpl=component\n\tlink=5a0c01671b25beb4458e56a34acec621b037aee2\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=5a0c01671b25beb4458e56a34acec621b037aee2\n\ta6ac2cb7aec6897cfa7ec8de6afe06a4=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tf503f79e0d72db80681dfac1bed19d12=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/style-and-layout-options/typography-mainmenu-27?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=918a1f49edaffd02fb859593925007e6f66b757a\n\ttmpl=component\n\tlink=918a1f49edaffd02fb859593925007e6f66b757a\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=918a1f49edaffd02fb859593925007e6f66b757a\n\tf58a65e81410532982c93b38874b9fe0=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t556b27054f8929eed6c0f1f426023e9d=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\ta05b8d21eba04d3853393a9cc2ebafa3=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/style-and-layout-options/google-fonts-enabled?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=a10f24087c1241d15bfd897a413fa69b163fb5dd\n\ttmpl=component\n\tlink=a10f24087c1241d15bfd897a413fa69b163fb5dd\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=a10f24087c1241d15bfd897a413fa69b163fb5dd\n\t973dec8b392a9be540dda26b1813162f=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tbecb5b460c3c2ae772b4161fd2f16d47=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tbc0b218fca779850010779be20ecb271=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t8976d6245fa096886f2ebfb904080f08=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/contact-us\n\tjform[contact_name]=\n\tjform[contact_email]=\n\tjform[contact_subject]=\n\tjform[contact_email_copy]=\n\toption=com_contact\n\ttask=contact.submit\n\treturn=\n\tid=1:name\n\tc365642b73c7309bff50ec3ce878645b=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tc365642b73c7309bff50ec3ce878645b=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: http://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/links-mainmenu-23/32-joomla-specific-links\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tba9e950289b7f1e440ae3b3153179c96=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/links-mainmenu-23/32-joomla-specific-links?format=feed&amp;type=rss\n\ttype=rss\n\tformat=feed\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/links-mainmenu-23/32-joomla-specific-links?format=feed&amp;type=atom\n\ttype=atom\n\tformat=feed\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/links-mainmenu-23?task=weblink.go&amp;id=1\n\ttask=weblink.go\n\tid=1\n\nForm: /\n\tsearchword=Search...\n\ttask=search\n\toption=com_search\n\tItemid=122\n\nhttp://tehnologiyaklimata.com/templates/joomla12/css/3_layout.css?v=1\n\tv=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/links-mainmenu-23?task=weblink.go&amp;id=4\n\ttask=weblink.go\n\tid=4\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/index.php?option=com_content&amp;view=article&amp;id=52&amp;Itemid=1\n\tid=52\n\tItemid=1\n\toption=com_content\n\tview=article\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/index.php?option=com_content&amp;view=article&amp;id=53&amp;Itemid=76\n\tid=53\n\tItemid=76\n\toption=com_content\n\tview=article\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/index.php?option=com_content&amp;view=category&amp;layout=blog&amp;id=1&amp;Itemid=172\n\tid=1\n\tItemid=172\n\tlayout=blog\n\toption=com_content\n\tview=category\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/index.php?option=com_contact&amp;view=category&amp;catid=12&amp;Itemid=57\n\tItemid=57\n\toption=com_contact\n\tcatid=12\n\tview=category\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/links-mainmenu-23?task=weblink.go&amp;id=2\n\ttask=weblink.go\n\tid=2\n\nForm: /search.php\n\tpattern=\n\tshow=\n\nhttp://tehnologiyaklimata.com/cal.php?id=5377\n\tid=5377\n\nhttp://tehnologiyaklimata.com/cal.php?id=5644\n\tid=5644\n\nhttp://tehnologiyaklimata.com/cal.php?id=5631\n\tid=5631\n\nhttp://tehnologiyaklimata.com/cal.php?id=5695\n\tid=5695\n\nhttp://tehnologiyaklimata.com/cal.php?id=5704\n\tid=5704\n\nhttp://tehnologiyaklimata.com/cal.php?id=5634\n\tid=5634\n\nhttp://tehnologiyaklimata.com/cal.php?id=4720\n\tid=4720\n\nhttp://tehnologiyaklimata.com/cal.php?id=1745\n\tid=1745\n\nhttp://tehnologiyaklimata.com/cal.php?id=1860\n\tid=1860\n\nhttp://tehnologiyaklimata.com/cal.php?id=2301\n\tid=2301\n\nhttp://tehnologiyaklimata.com/cal.php?id=2814\n\tid=2814\n\nhttp://tehnologiyaklimata.com/cal.php?id=3294\n\tid=3294\n\nhttp://tehnologiyaklimata.com/cal.php?id=5527\n\tid=5527\n\nhttp://tehnologiyaklimata.com/cal.php?id=2352\n\tid=2352\n\nhttp://tehnologiyaklimata.com/cal.php?id=2682\n\tid=2682\n\nhttp://tehnologiyaklimata.com/cal.php?id=3793\n\tid=3793\n\nhttp://tehnologiyaklimata.com/cal.php?id=5473\n\tid=5473\n\nhttp://tehnologiyaklimata.com/cal.php?id=109\n\tid=109\n\nhttp://tehnologiyaklimata.com/cal.php?id=272\n\tid=272\n\nhttp://tehnologiyaklimata.com/cal.php?id=561\n\tid=561\n\nhttp://tehnologiyaklimata.com/cal.php?id=1005\n\tid=1005\n\nhttp://tehnologiyaklimata.com/cal.php?id=1304\n\tid=1304\n\nhttp://tehnologiyaklimata.com/cal.php?id=1624\n\tid=1624\n\nhttp://tehnologiyaklimata.com/cal.php?id=1632\n\tid=1632\n\nhttp://tehnologiyaklimata.com/cal.php?id=1706\n\tid=1706\n\nhttp://tehnologiyaklimata.com/cal.php?id=1918\n\tid=1918\n\nhttp://tehnologiyaklimata.com/cal.php?id=2017\n\tid=2017\n\nhttp://tehnologiyaklimata.com/cal.php?id=2418\n\tid=2418\n\nhttp://tehnologiyaklimata.com/cal.php?id=2932\n\tid=2932\n\nhttp://tehnologiyaklimata.com/cal.php?id=3416\n\tid=3416\n\nhttp://tehnologiyaklimata.com/cal.php?id=3861\n\tid=3861\n\nhttp://tehnologiyaklimata.com/cal.php?id=4014\n\tid=4014\n\nhttp://tehnologiyaklimata.com/cal.php?id=4147\n\tid=4147\n\nhttp://tehnologiyaklimata.com/cal.php?id=5519\n\tid=5519\n\nhttp://tehnologiyaklimata.com/cal.php?id=4799\n\tid=4799\n\nhttp://tehnologiyaklimata.com/cal.php?id=5685\n\tid=5685\n\nhttp://tehnologiyaklimata.com/cal.php?id=153\n\tid=153\n\nhttp://tehnologiyaklimata.com/cal.php?id=2663\n\tid=2663\n\nhttp://tehnologiyaklimata.com/cal.php?id=1732\n\tid=1732\n\nhttp://tehnologiyaklimata.com/cal.php?id=2580\n\tid=2580\n\nhttp://tehnologiyaklimata.com/cal.php?id=3722\n\tid=3722\n\nhttp://tehnologiyaklimata.com/cal.php?id=4258\n\tid=4258\n\nhttp://tehnologiyaklimata.com/cal.php?id=3760\n\tid=3760\n\nhttp://tehnologiyaklimata.com/cal.php?id=4308\n\tid=4308\n\nhttp://tehnologiyaklimata.com/cal.php?id=1385\n\tid=1385\n\nhttp://tehnologiyaklimata.com/cal.php?id=1523\n\tid=1523\n\nhttp://tehnologiyaklimata.com/cal.php?id=1670\n\tid=1670\n\nhttp://tehnologiyaklimata.com/cal.php?id=1665\n\tid=1665\n\nhttp://tehnologiyaklimata.com/cal.php?id=1847\n\tid=1847\n\nhttp://tehnologiyaklimata.com/cal.php?id=3643\n\tid=3643\n\nhttp://tehnologiyaklimata.com/cal.php?id=3980\n\tid=3980\n\nhttp://tehnologiyaklimata.com/cal.php?id=5701\n\tid=5701\n\nhttp://tehnologiyaklimata.com/cal.php?id=3684\n\tid=3684\n\nhttp://tehnologiyaklimata.com/cal.php?id=4222\n\tid=4222\n\nhttp://tehnologiyaklimata.com/cal.php?id=4751\n\tid=4751\n\nhttp://tehnologiyaklimata.com/cal.php?id=5017\n\tid=5017\n\nhttp://tehnologiyaklimata.com/cal.php?id=5212\n\tid=5212\n\nhttp://tehnologiyaklimata.com/cal.php?id=5388\n\tid=5388\n\nhttp://tehnologiyaklimata.com/cal.php?id=1652\n\tid=1652\n\nhttp://tehnologiyaklimata.com/cal.php?id=1848\n\tid=1848\n\nhttp://tehnologiyaklimata.com/cal.php?id=1946\n\tid=1946\n\nhttp://tehnologiyaklimata.com/cal.php?id=4636\n\tid=4636\n\nhttp://tehnologiyaklimata.com/cal.php?id=5322\n\tid=5322\n\nhttp://tehnologiyaklimata.com/cal.php?id=1346\n\tid=1346\n\nhttp://tehnologiyaklimata.com/cal.php?id=1671\n\tid=1671\n\nhttp://tehnologiyaklimata.com/cal.php?id=2449\n\tid=2449\n\nhttp://tehnologiyaklimata.com/cal.php?id=5401\n\tid=5401\n\nhttp://tehnologiyaklimata.com/cal.php?id=5533\n\tid=5533\n\nhttp://tehnologiyaklimata.com/cal.php?id=2246\n\tid=2246\n\nhttp://tehnologiyaklimata.com/cal.php?id=3708\n\tid=3708\n\nhttp://tehnologiyaklimata.com/cal.php?id=3761\n\tid=3761\n\nhttp://tehnologiyaklimata.com/cal.php?id=4725\n\tid=4725\n\nhttp://tehnologiyaklimata.com/cal.php?id=5222\n\tid=5222\n\nhttp://tehnologiyaklimata.com/cal.php?id=5368\n\tid=5368\n\nhttp://tehnologiyaklimata.com/cal.php?id=5370\n\tid=5370\n\nhttp://tehnologiyaklimata.com/cal.php?id=5474\n\tid=5474\n\nhttp://tehnologiyaklimata.com/cal.php?id=1545\n\tid=1545\n\nhttp://tehnologiyaklimata.com/cal.php?id=1546\n\tid=1546\n\nhttp://tehnologiyaklimata.com/cal.php?id=2208\n\tid=2208\n\nhttp://tehnologiyaklimata.com/cal.php?id=3925\n\tid=3925\n\nhttp://tehnologiyaklimata.com/cal.php?id=1704\n\tid=1704\n\nhttp://tehnologiyaklimata.com/cal.php?id=1719\n\tid=1719\n\nhttp://tehnologiyaklimata.com/cal.php?id=1820\n\tid=1820\n\nhttp://tehnologiyaklimata.com/cal.php?id=4507\n\tid=4507\n\nhttp://tehnologiyaklimata.com/cal.php?id=5372\n\tid=5372\n\nhttp://tehnologiyaklimata.com/cal.php?id=5092\n\tid=5092\n\nhttp://tehnologiyaklimata.com/cal.php?id=5343\n\tid=5343\n\nhttp://tehnologiyaklimata.com/cal.php?id=5427\n\tid=5427\n\nhttp://tehnologiyaklimata.com/cal.php?id=1099\n\tid=1099\n\nhttp://tehnologiyaklimata.com/cal.php?id=4648\n\tid=4648\n\nhttp://tehnologiyaklimata.com/cal.php?id=409\n\tid=409\n\nhttp://tehnologiyaklimata.com/cal.php?id=384\n\tid=384\n\nhttp://tehnologiyaklimata.com/cal.php?id=2527\n\tid=2527\n\nhttp://tehnologiyaklimata.com/cal.php?id=2600\n\tid=2600\n\nhttp://tehnologiyaklimata.com/cal.php?id=2660\n\tid=2660\n\nhttp://tehnologiyaklimata.com/cal.php?id=3075\n\tid=3075\n\nhttp://tehnologiyaklimata.com/cal.php?id=3653\n\tid=3653\n\nhttp://tehnologiyaklimata.com/cal.php?id=4626\n\tid=4626\n\nhttp://tehnologiyaklimata.com/cal.php?id=5276\n\tid=5276\n\nhttp://tehnologiyaklimata.com/cal.php?id=2500\n\tid=2500\n\nhttp://tehnologiyaklimata.com/cal.php?id=4922\n\tid=4922\n\nhttp://tehnologiyaklimata.com/cal.php?id=5135\n\tid=5135\n\nhttp://tehnologiyaklimata.com/cal.php?id=5313\n\tid=5313\n\nhttp://tehnologiyaklimata.com/cal.php?id=1316\n\tid=1316\n\nhttp://tehnologiyaklimata.com/cal.php?id=1708\n\tid=1708\n\nhttp://tehnologiyaklimata.com/cal.php?id=4256\n\tid=4256\n\nhttp://tehnologiyaklimata.com/cal.php?id=5052\n\tid=5052\n\nhttp://tehnologiyaklimata.com/cal.php?id=2662\n\tid=2662\n\nhttp://tehnologiyaklimata.com/cal.php?id=3422\n\tid=3422\n\nhttp://tehnologiyaklimata.com/cal.php?id=4019\n\tid=4019\n\nhttp://tehnologiyaklimata.com/cal.php?id=338\n\tid=338\n\nhttp://tehnologiyaklimata.com/cal.php?id=456\n\tid=456\n\nhttp://tehnologiyaklimata.com/cal.php?id=641\n\tid=641\n\nhttp://tehnologiyaklimata.com/cal.php?id=998\n\tid=998\n\nhttp://tehnologiyaklimata.com/cal.php?id=1198\n\tid=1198\n\nhttp://tehnologiyaklimata.com/cal.php?id=1360\n\tid=1360\n\nhttp://tehnologiyaklimata.com/cal.php?id=1981\n\tid=1981\n\nhttp://tehnologiyaklimata.com/cal.php?id=2051\n\tid=2051\n\nhttp://tehnologiyaklimata.com/cal.php?id=3053\n\tid=3053\n\nhttp://tehnologiyaklimata.com/cal.php?id=5298\n\tid=5298\n\nhttp://tehnologiyaklimata.com/cal.php?id=5712\n\tid=5712\n\nhttp://tehnologiyaklimata.com/cal.php?id=5713\n\tid=5713\n\nhttp://tehnologiyaklimata.com/cal.php?id=5714\n\tid=5714\n\nhttp://tehnologiyaklimata.com/cal.php?id=5716\n\tid=5716\n\nhttp://tehnologiyaklimata.com/cal.php?id=5697\n\tid=5697\n\nhttp://tehnologiyaklimata.com/cal.php?id=841\n\tid=841\n\nhttp://tehnologiyaklimata.com/cal.php?id=1490\n\tid=1490\n\nhttp://tehnologiyaklimata.com/cal.php?id=5649\n\tid=5649\n\nhttp://tehnologiyaklimata.com/cal.php?id=5305\n\tid=5305\n\nhttp://tehnologiyaklimata.com/cal.php?id=5380\n\tid=5380\n\nhttp://tehnologiyaklimata.com/cal.php?id=1516\n\tid=1516\n\nhttp://tehnologiyaklimata.com/cal.php?id=1466\n\tid=1466\n\nhttp://tehnologiyaklimata.com/cal.php?id=1583\n\tid=1583\n\nhttp://tehnologiyaklimata.com/cal.php?id=5521\n\tid=5521\n\nhttp://tehnologiyaklimata.com/cal.php?id=5700\n\tid=5700\n\nhttp://tehnologiyaklimata.com/cal.php?id=3385\n\tid=3385\n\nhttp://tehnologiyaklimata.com/cal.php?id=5381\n\tid=5381\n\nhttp://tehnologiyaklimata.com/cal.php?id=3386\n\tid=3386\n\nhttp://tehnologiyaklimata.com/cal.php?id=1200\n\tid=1200\n\nhttp://tehnologiyaklimata.com/cal.php?id=2589\n\tid=2589\n\nhttp://tehnologiyaklimata.com/cal.php?id=5715\n\tid=5715\n\nhttp://tehnologiyaklimata.com/cal.php?id=1389\n\tid=1389\n\nhttp://tehnologiyaklimata.com/cal.php?id=5382\n\tid=5382\n\nhttp://tehnologiyaklimata.com/cal.php?id=2408\n\tid=2408\n\nhttp://tehnologiyaklimata.com/cal.php?id=5699\n\tid=5699\n\nhttp://tehnologiyaklimata.com/cal.php?id=5384\n\tid=5384\n\nhttp://tehnologiyaklimata.com/cal.php?id=231\n\tid=231\n\nhttp://tehnologiyaklimata.com/cal.php?id=5717\n\tid=5717\n\nhttp://tehnologiyaklimata.com/cal.php?id=1137\n\tid=1137\n\nhttp://tehnologiyaklimata.com/cal.php?id=4220\n\tid=4220\n\nhttp://tehnologiyaklimata.com/source.php?url=/index.php\n\turl=/index.php\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/links-mainmenu-23?task=weblink.go&amp;id=5\n\ttask=weblink.go\n\tid=5\n\nForm: http://forum.joomla.org/results.php\n\tcx=007628682600509520926:higrppcfurc\n\tcof=FORID:9\n\tq=\n\nForm: ./ucp.php?mode=login&sid=d2a749f56082e64bf914fce02498fdef\n\tusername=\n\tpassword=\n\tautologin=\n\tlogin=Login\n\tredirect=./index.php?sid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/index.php?sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/ucp.php?mode=register&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=register\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/faq.php?sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=leaders&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=leaders\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/ucp.php?mode=login&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=login\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=504&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=504\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=8&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=8\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=9&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=9\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=9&amp;p=3039062&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039062\n\tp=3039062\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=9\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=2567&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=2567\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=614&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=614\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=615&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=615\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=group&amp;g=41&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=group\n\tg=41\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=615&amp;p=3039727&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039727\n\tp=3039727\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=615\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=407922&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=407922\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=622&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=622\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=623&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=623\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=622&amp;p=3039730&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039730\n\tp=3039730\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=622\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=246141&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=246141\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=624&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=624\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=673&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=673\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=624&amp;p=3039707&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039707\n\tp=3039707\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=624\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=711046&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=711046\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=625&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=625\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=625&amp;p=3039639&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039639\n\tp=3039639\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=625\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=562041&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=562041\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=621&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=621\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=28000&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=28000\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=67439&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=67439\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=621&amp;p=3039690&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039690\n\tp=3039690\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=621\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=710722&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=710722\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=620&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=620\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=471&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=471\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=626&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=626\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=627&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=627\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=628&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=628\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=627&amp;p=3039701&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039701\n\tp=3039701\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=627\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=709632&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=709632\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=619&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=619\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=629&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=629\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=674&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=674\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=675&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=675\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=676&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=676\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=677&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=677\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=678&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=678\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=680&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=680\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=678&amp;p=3039714&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039714\n\tp=3039714\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=678\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=704837&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=704837\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=618&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=618\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=618&amp;p=3039719&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039719\n\tp=3039719\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=618\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=617&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=617\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=14&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=14\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=617&amp;p=3039569&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039569\n\tp=3039569\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=617\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=656384&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=656384\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=616&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=616\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=616&amp;p=3039658&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039658\n\tp=3039658\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=616\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=705&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=705\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=706&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=706\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=706&amp;p=3039725&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039725\n\tp=3039725\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=706\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=349940&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=349940\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=707&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=707\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=717&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=717\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=717&amp;p=3039663&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039663\n\tp=3039663\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=717\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=711025&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=711025\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=708&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=708\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=719&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=719\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=708&amp;p=3039673&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039673\n\tp=3039673\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=708\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=251498&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=251498\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=710&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=710\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=710&amp;p=3039677&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039677\n\tp=3039677\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=710\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=711033&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=711033\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=714&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=714\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=714&amp;p=3039617&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039617\n\tp=3039617\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=714\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=79666&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=79666\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=715&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=715\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=721&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=721\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=724&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=724\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=723&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=723\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=715&amp;p=3039679&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039679\n\tp=3039679\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=715\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=711036&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=711036\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=713&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=713\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=718&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=718\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=726&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=726\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=725&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=725\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=720&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=720\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=722&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=722\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=718&amp;p=3039687&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039687\n\tp=3039687\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=718\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=704282&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=704282\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=712&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=712\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=712&amp;p=3039595&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039595\n\tp=3039595\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=712\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=644324&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=644324\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=711&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=711\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=711&amp;p=3039465&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039465\n\tp=3039465\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=711\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=122462&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=122462\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=709&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=709\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=709&amp;p=3039671&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039671\n\tp=3039671\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=709\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=711031&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=711031\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=513&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=513\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=506&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=506\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=428&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=428\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=429&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=429\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=543&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=543\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=431&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=431\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=430&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=430\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=432&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=432\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=470&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=470\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=471&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=471\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=472&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=472\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=473&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=473\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=474&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=474\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=466&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=466\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=469&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=469\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=404&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=404\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=480&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=480\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=541&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=541\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=544&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=544\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=485&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=485\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=433&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=433\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=428&amp;p=3039708&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039708\n\tp=3039708\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=428\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=710880&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=710880\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=505&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=505\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=35&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=35\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=36&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=36\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=267&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=267\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=296&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=296\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=34&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=34\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=465&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=465\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=39&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=39\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=40&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=40\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=41&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=41\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=57&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=57\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=43&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=43\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=216&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=216\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=42&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=42\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=309&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=309\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=123&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=123\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=268&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=268\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=32&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=32\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=32&amp;p=3039699&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039699\n\tp=3039699\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=32\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=711041&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=711041\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=542&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=542\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=511&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=511\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=243&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=243\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=17&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=17\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=568&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=568\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=274&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=274\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=753&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=753\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=64&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=64\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=104&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=104\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=95&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=95\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=98&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=98\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=150&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=150\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=16&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=16\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=13&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=13\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=21&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=21\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=25&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=25\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=19&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=19\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=14&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=14\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=50&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=50\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=172&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=172\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=403&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=403\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=162&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=162\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=53&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=53\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=55&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=55\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=141&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=141\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=569&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=569\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=358&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=358\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=189&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=189\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=215&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=215\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=191&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=191\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=97&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=97\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=102&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=102\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=18&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=18\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=23&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=23\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=30&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=30\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=26&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=26\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=29&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=29\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=402&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=402\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=157&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=157\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=24&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=24\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=63&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=63\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=545&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=545\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=15&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=15\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=51&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=51\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=340&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=340\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=171&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=171\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=151&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=151\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=26&amp;p=3039637&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039637\n\tp=3039637\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=26\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=711021&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=711021\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=507&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=507\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=46&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=46\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=19591&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=19591\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=46&amp;p=3039670&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039670\n\tp=3039670\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=46\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=682521&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=682521\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=306&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=306\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=94&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=94\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=group&amp;g=20&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=group\n\tg=20\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=306&amp;p=2995461&amp;sid=d2a749f56082e64bf914fce02498fdef#p2995461\n\tp=2995461\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=306\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=698542&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=698542\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=514&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=514\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=514&amp;p=3039069&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039069\n\tp=3039069\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=514\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=710867&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=710867\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=48&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=48\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=48&amp;p=3039667&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039667\n\tp=3039667\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=48\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=685653&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=685653\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=562&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=562\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=128178&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=128178\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=562&amp;p=3037826&amp;sid=d2a749f56082e64bf914fce02498fdef#p3037826\n\tp=3037826\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=562\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=705940&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=705940\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=571&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=571\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=group&amp;g=50&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=group\n\tg=50\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=571&amp;p=3039470&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039470\n\tp=3039470\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=571\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=710991&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=710991\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=575&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=575\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=46545&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=46545\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=575&amp;p=3034687&amp;sid=d2a749f56082e64bf914fce02498fdef#p3034687\n\tp=3034687\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=575\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=709771&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=709771\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=704&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=704\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=704&amp;p=3039720&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039720\n\tp=3039720\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=704\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=711049&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=711049\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=681&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=681\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=196316&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=196316\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=208380&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=208380\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=681&amp;p=3026887&amp;sid=d2a749f56082e64bf914fce02498fdef#p3026887\n\tp=3026887\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=681\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=646&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=646\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=509&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=509\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=218211&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=218211\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=727&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=727\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=642&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=642\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=728&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=728\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=304&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=304\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=579&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=579\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=199&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=199\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=703&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=703\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=728&amp;p=3039558&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039558\n\tp=3039558\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=728\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=711010&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=711010\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=508&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=508\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=262&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=262\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=group&amp;g=16&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=group\n\tg=16\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=262&amp;p=3039681&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039681\n\tp=3039681\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=262\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=12331&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=12331\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=563&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=563\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=150802&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=150802\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=12833&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=12833\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=293859&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=293859\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=563&amp;p=3039601&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039601\n\tp=3039601\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=563\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=688668&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=688668\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=303&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=303\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=143&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=143\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=6495&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=6495\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=303&amp;p=3030384&amp;sid=d2a749f56082e64bf914fce02498fdef#p3030384\n\tp=3030384\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=303\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=7&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=7\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=166991&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=166991\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=406&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=406\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=364&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=364\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=7&amp;p=3039275&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039275\n\tp=3039275\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=7\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=701&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=701\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=19536&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=19536\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=701&amp;p=3039724&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039724\n\tp=3039724\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=701\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=573&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=573\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=251146&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=251146\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=574&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=574\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=573&amp;p=2926056&amp;sid=d2a749f56082e64bf914fce02498fdef#p2926056\n\tp=2926056\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=573\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=685271&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=685271\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=510&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=510\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=11&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=11\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=381&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=381\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewforum.php?f=699&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=699\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/viewtopic.php?f=381&amp;p=3039705&amp;sid=d2a749f56082e64bf914fce02498fdef#p3039705\n\tp=3039705\n\tsid=d2a749f56082e64bf914fce02498fdef\n\tf=381\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=711043&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=711043\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/ucp.php?mode=delete_cookies&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=delete_cookies\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=710463&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=710463\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=711050&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=711050\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=173815&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=173815\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=391403&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=391403\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=25232&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=25232\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=595011&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=595011\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=674062&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=674062\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=711048&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=711048\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=710783&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=710783\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=535039&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=535039\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=395769&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=395769\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=465111&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=465111\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=37344&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=37344\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=141449&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=141449\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=620200&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=620200\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=709645&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=709645\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=710482&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=710482\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=678541&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=678541\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=456546&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=456546\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=616434&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=616434\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=viewprofile&amp;u=708924&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tu=708924\n\tmode=viewprofile\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=group&amp;g=10&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=group\n\tg=10\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=group&amp;g=19&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=group\n\tg=19\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=group&amp;g=11&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=group\n\tg=11\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=group&amp;g=34&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=group\n\tg=34\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=group&amp;g=13&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=group\n\tg=13\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/memberlist.php?mode=group&amp;g=47&amp;sid=d2a749f56082e64bf914fce02498fdef\n\tmode=group\n\tg=47\n\tsid=d2a749f56082e64bf914fce02498fdef\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/links-mainmenu-23?task=weblink.go&amp;id=3\n\ttask=weblink.go\n\tid=3\n\nForm: http://search.oracle.com/search/search\n\tq=Search\n\tgroup=MySQL\n\nForm: http://search.oracle.com/search/search\n\tq=\n\tgroup=MySQL\n\nhttp://tehnologiyaklimata.com/common/css/mysql.css?v=20121230\n\tv=20121230\n\nhttp://tehnologiyaklimata.com/common/css/print.css?v=20111230\n\tv=20111230\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/links-mainmenu-23?task=weblink.go&amp;id=3#mainContent\n\ttask=weblink.go\n\tid=3\n\nForm: http://search.oracle.com/search/search\n\tq=Search\n\tgroup=MySQL\n\nForm: http://search.oracle.com/search/search\n\tq=\n\tgroup=MySQL\n\nhttp://tehnologiyaklimata.com/news-and-events/web-seminars/rss.php?webinars=ondemand&amp;language=en\n\twebinars=ondemand\n\tlanguage=en\n\nhttp://tehnologiyaklimata.com/click.php?e=35350\n\te=35350\n\nhttp://tehnologiyaklimata.com/click.php?e=35174\n\te=35174\n\nhttp://tehnologiyaklimata.com/click.php?e=35471\n\te=35471\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/links-mainmenu-23?task=weblink.go&amp;id=6\n\ttask=weblink.go\n\tid=6\n\nForm: /p\n\tq=\n\tsearch_type=projects\n\nhttp://tehnologiyaklimata.com/p/joomla/commits?sort=oldest\n\tsort=oldest\n\nhttp://tehnologiyaklimata.com/p/joomla/commits?time_span=30+days\n\ttime_span=30 days\n\nhttp://tehnologiyaklimata.com/p/joomla/contributors?sort=latest_commit&amp;time_span=30+days\n\tsort=latest_commit\n\ttime_span=30 days\n\nhttp://tehnologiyaklimata.com/p/joomla/contributors?highlight_key=first_checkin&amp;time_span=30+days\n\thighlight_key=first_checkin\n\ttime_span=30 days\n\nhttp://tehnologiyaklimata.com/p/joomla/commits?time_span=12+months\n\ttime_span=12 months\n\nhttp://tehnologiyaklimata.com/p/joomla/contributors?sort=latest_commit&amp;time_span=12+months\n\tsort=latest_commit\n\ttime_span=12 months\n\nhttp://tehnologiyaklimata.com/p/joomla/contributors?sort=latest_commit\n\tsort=latest_commit\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: http://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/links-mainmenu-23/32-joomla-specific-links\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t4d96b5f63d29702015bed4d29b1fbad2=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t808304d8d159191d9624121876f5d737=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: http://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-feeds-mainmenu-7/31-related-projects\n\tlimit=\n\tfilter_order=ordering\n\tfilter_order_Dir=ASC\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\ta1009ba4d881e8b1f55a8de161f09d82=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t4a0ed18f1cc37c185a98feafd5737df0=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t70a50e87c99935be28cfd30b3d6f9ec2=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tf30eae4e6a1d7b7db7d20cd76637a947=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tcd2c49fac5b2261371489e4e97642b4f=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: http://tehnologiyaklimata.com/index.php?option=com_newsfeeds&view=category&id=31&Itemid=105\n\tlimit=\n\tfilter_order=ordering\n\tfilter_order_Dir=ASC\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t6e34806a9ae912661f69a58263eacfaf=1\n\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 7537\n\temail_address=\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t217fdffa108d5fd1ec3f02b2144c7b10=1\n\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 6736\n\temail_address=\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t48df4e3c29b16aeb08dcc17bd6e814d3=1\n\nhttp://tehnologiyaklimata.com/index.php/21-frontpage/frontpage/249-polypropylene?tmpl=component&amp;print=1&amp;page=\n\tprint=1\n\ttmpl=component\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=dbd700a4299aa858ee9fd8d8217bd20231718e87\n\ttmpl=component\n\tlink=dbd700a4299aa858ee9fd8d8217bd20231718e87\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=dbd700a4299aa858ee9fd8d8217bd20231718e87\n\ta26983a0e3c5f8b39dd6a23d346872d5=1\n\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 1933\n\temail_address=\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t87d8f94ffc561be1409c12ed209c335f=1\n\nhttp://tehnologiyaklimata.com/index.php/21-frontpage/frontpage/251-stainless-steel?tmpl=component&amp;print=1&amp;page=\n\tprint=1\n\ttmpl=component\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=d76d7da82b80d2625e81a381363a047ae39cc08c\n\ttmpl=component\n\tlink=d76d7da82b80d2625e81a381363a047ae39cc08c\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=d76d7da82b80d2625e81a381363a047ae39cc08c\n\t400bfdc31976ca98da70ad6e18db027f=1\n\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 7714\n\temail_address=\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tb3778b6fd7ad98dd7c4044dc0089bf04=1\n\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 9212\n\temail_address=\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t43693255542dfed7936df2f7a5c905da=1\n\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 7195\n\temail_address=\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t478274244ed1a9e3f9e6cf8d90f4f5ee=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t7859959dded863f40207dbae36857044=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t0283583e83e624c9c2a3ace6d4a49ea8=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: http://tehnologiyaklimata.com/index.php?option=com_newsfeeds&view=category&id=31&Itemid=105\n\tlimit=\n\tfilter_order=ordering\n\tfilter_order_Dir=ASC\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\td1f96a76744c85670c9c54eb8300dc57=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: http://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-feeds-mainmenu-7/31-related-projects\n\tlimit=\n\tfilter_order=ordering\n\tfilter_order_Dir=ASC\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t68707f7d6d7cf04a0d1cddea5f6b08d6=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: http://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-feeds-mainmenu-7/30-free-and-open-source-software\n\tlimit=\n\tfilter_order=ordering\n\tfilter_order_Dir=ASC\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t225caed2bd77d5c804bb5ee7b1b0e018=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tf4d2b52929f01b88116b4c7f18a37f01=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t09cb761b354ddaea7fd16f8f9c330181=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t5fc72b6e4baa2d46a00d640606d2712d=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t690b8c9ba3d2c606fcc5772a308dc6a1=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t785d9ff60ae6efc2e2751a70c5de2613=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tdf9e6714045384b00c90eace889deb54=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t7e38001b6544544b23a4d8a5b60bbbdc=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t1268c9f604e35d3a4e047c574a2f5d0c=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t15989d39a5dda1e6867b484ee21033bb=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t5a8d946b543040b338be8d63e7ceda18=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: http://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-feeds-mainmenu-7/30-free-and-open-source-software\n\tlimit=\n\tfilter_order=ordering\n\tfilter_order_Dir=ASC\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\ta0c7f1e544a3c02eff39ef50734811ea=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: http://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-feeds-mainmenu-7/29-joomla\n\tlimit=\n\tfilter_order=ordering\n\tfilter_order_Dir=ASC\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tf2c79c32d29b5024c537cb76fd8419d9=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tcf9fd02d9ffed8fdd6436b0f5bdb4bc5=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tf8b60d61264c570f85689d7e5f549d44=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\ta81b45449b359a9efeb0b05c1edfa896=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tf89df9ed464b4b97d3b22b984f0775bc=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t2f4cf4666105aef9c6ba60fc76a85763=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t5f2a9647ed240fd7469e5a37ecbcaaab=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\ta069c6faf39d51af6f015551fe1c0d33=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tb60acef48d9901fb3ebd0ff61a214ff1=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tc241a14f17226851a7010b19286400ae=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tbb37339de3151b7bf7ef182c806e09dc=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: http://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-feeds-mainmenu-7/29-joomla\n\tlimit=\n\tfilter_order=ordering\n\tfilter_order_Dir=ASC\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t9d758adda82cfb7f00a6eac88092699b=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t65d9958aa95650c5f4618b7624d90597=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/template-features/drop-down-panel?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=787279e2006f2b956be3cc885b50847150243e8a\n\ttmpl=component\n\tlink=787279e2006f2b956be3cc885b50847150243e8a\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=787279e2006f2b956be3cc885b50847150243e8a\n\tac630d4f8eeae4956530130d62d60815=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/template-features/hide-article-component-area?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=a3129a75fabd50b5d555afd016bfb5549930225e\n\ttmpl=component\n\tlink=a3129a75fabd50b5d555afd016bfb5549930225e\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=a3129a75fabd50b5d555afd016bfb5549930225e\n\t0b799cca1ccb2822d64f228f72c14fd0=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/template-features/menu-scroll-to?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=1a806dff1f903fed222f104d477f8e86c7c354d6\n\ttmpl=component\n\tlink=1a806dff1f903fed222f104d477f8e86c7c354d6\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=1a806dff1f903fed222f104d477f8e86c7c354d6\n\t1eaa66fa0ccd2a8c081d821aa660e065=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?start=5\n\tstart=5\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tb787b677c3e829828a568d898e0ca28f=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/template-features/mobile-device-ready?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=ae1a7de7671526c48d68425bc0293679ed082ff1\n\ttmpl=component\n\tlink=ae1a7de7671526c48d68425bc0293679ed082ff1\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=ae1a7de7671526c48d68425bc0293679ed082ff1\n\t6383834a9ffd9bd475be0ee74544591b=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/template-specific-features?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=7a10345a7574f280aeede37ada1cbfea8632a838\n\ttmpl=component\n\tlink=7a10345a7574f280aeede37ada1cbfea8632a838\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=7a10345a7574f280aeede37ada1cbfea8632a838\n\ta835a0e18dc44bbbc9fa64337776da1c=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?limitstart=0\n\tlimitstart=0\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tc1331fa2c6ac88f036041e1ef7c02220=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?start=10\n\tstart=10\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t3c44b38ba513b084d7b217484af75b6c=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/template-features/seo-optimized?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=ab48927250da87ca6a83fd60dfdb8718783d5507\n\ttmpl=component\n\tlink=ab48927250da87ca6a83fd60dfdb8718783d5507\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=ab48927250da87ca6a83fd60dfdb8718783d5507\n\t35a05b3bb2fed30e460e49532bf7a82e=1\n\nhttp://tehnologiyaklimata.com/index.php/extensions/s5-accordion-menu?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=d2fe90e05462c94169cd20759f76a20edfddac40\n\ttmpl=component\n\tlink=d2fe90e05462c94169cd20759f76a20edfddac40\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=d2fe90e05462c94169cd20759f76a20edfddac40\n\te64808041956eb7b247e1db927e956aa=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?start=15\n\tstart=15\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t6ac09093c2ae17629dcbddcb69ff270c=1\n\nhttp://tehnologiyaklimata.com/index.php/extensions/s5-cssjs-compressor?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=d3ecbe3d58a7209efc040c535740e0273ba2f1bf\n\ttmpl=component\n\tlink=d3ecbe3d58a7209efc040c535740e0273ba2f1bf\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=d3ecbe3d58a7209efc040c535740e0273ba2f1bf\n\t487710293efa7786436a909b38e6936d=1\n\nhttp://tehnologiyaklimata.com/index.php/extensions/s5-tab-show?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=39e920b955f2fe9d5f193909c4ad0a7c0001e369\n\ttmpl=component\n\tlink=39e920b955f2fe9d5f193909c4ad0a7c0001e369\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=39e920b955f2fe9d5f193909c4ad0a7c0001e369\n\t0f4c8fbcd4b4e366b632e35c1bb34309=1\n\nhttp://tehnologiyaklimata.com/index.php/extensions/s5-image-and-content-fader?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=563bfd4e89f547c53e57e35de6878e73e68b5f9b\n\ttmpl=component\n\tlink=563bfd4e89f547c53e57e35de6878e73e68b5f9b\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=563bfd4e89f547c53e57e35de6878e73e68b5f9b\n\t45db541e2b4b5505b61fc1b30ea5eb49=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?start=20\n\tstart=20\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tb3c33c0f4e5059ad3e5bbff11e9981bd=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?start=25\n\tstart=25\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t5801d137c1fef9416d441e9f4dabe92c=1\n\nhttp://tehnologiyaklimata.com/index.php/s5-flex-menu-21919?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=da4da5a3d9570d900605f2a6e43ec15fe8f34171\n\ttmpl=component\n\tlink=da4da5a3d9570d900605f2a6e43ec15fe8f34171\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=da4da5a3d9570d900605f2a6e43ec15fe8f34171\n\tf5e57875c21afda020fc4f8d09307f16=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?start=25#s5_scrolltotop\n\tstart=25\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t1d5e769a222cee60a439eb5bc9a2cb0e=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?start=20#s5_scrolltotop\n\tstart=20\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t4e2dcf201988097a412043656dd802ee=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?start=15#s5_scrolltotop\n\tstart=15\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t587176d155dd4b89b1d80013172ca84d=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?start=10#s5_scrolltotop\n\tstart=10\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t310b5ece2cc94f19ca290983bf5dc926=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?limitstart=0#s5_scrolltotop\n\tlimitstart=0\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tdd788d1f08db058f831c38796b6a0de4=1\n\nhttp://tehnologiyaklimata.com/index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/news-mainmenu-2?start=5#s5_scrolltotop\n\tstart=5\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t510af90db5e9ec5ad6cb70825279a201=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t19ef7be89165f14b7b77ce82b29deb8b=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/features-mainmenu-47/joomla-stuff-mainmenu-26/search-mainmenu-5\n\tsearchword=\n\ttask=search\n\tsearchphrase=all\n\tsearchphrase=any\n\tsearchphrase=exact\n\tordering=\n\tareas[]=categories\n\tareas[]=contacts\n\tareas[]=content\n\tareas[]=newsfeeds\n\tareas[]=weblinks\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tadbe94d83d9ee29b09c25b4bf1ddedfa=1\n\nhttp://tehnologiyaklimata.com/?lang=rtl#s5_scrolltotop\n\tlang=rtl\n\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 8877\n\temail_address=\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tc6e78f1a56618ad797912e11c24f00df=1\n\nhttp://tehnologiyaklimata.com/?lang=ltr#s5_scrolltotop\n\tlang=ltr\n\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 6470\n\temail_address=\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t57270cdcb2975c5090996a035f6d1654=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t165afe88aa3e189aa8aef56168ab8b95=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t70839bbf6679f901087c0cf4e343eaa3=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\te8349d4259e89850170416375c5212a2=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t74348322ca07ebd13a5df0f961c55729=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tb0ad3b85f242666422a1a13772c08aaf=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tc2b49cb5bed0f9dbfdd6febd61c7f333=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tfa8eed004540d3aa2e8dbd45784a3618=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tadc0640c1dbd81acb716392f70e87d57=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t6b9a0fcc5a87558b409a9d036b9fd9ea=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t66fbcdde9096bff667bec9eb0cfb6d78=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t497fc2d34c0538909a911ce7a0f8c324=1\n\nhttp://tehnologiyaklimata.com/index.php/extensions/s5-map-it-with-google-v2?tmpl=component&amp;print=1&amp;layout=default&amp;page=\n\tprint=1\n\ttmpl=component\n\tlayout=default\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t16f4b07bd2824c6d1bf8edbfc4559d9d=1\n\nhttp://tehnologiyaklimata.com/index.php/extensions/s5-map-it-with-google-v2/2-uncategorised?format=feed&amp;type=rss\n\ttype=rss\n\tformat=feed\n\nhttp://tehnologiyaklimata.com/index.php/extensions/s5-map-it-with-google-v2/2-uncategorised?format=feed&amp;type=atom\n\ttype=atom\n\tformat=feed\n\nhttp://tehnologiyaklimata.com/index.php/component/mailto/?tmpl=component&amp;template=construction&amp;link=ef66622a24bd21112778f6a125d7c88ec019f726\n\ttmpl=component\n\tlink=ef66622a24bd21112778f6a125d7c88ec019f726\n\ttemplate=construction\n\nForm: http://tehnologiyaklimata.com/index.php\n\tmailto=\n\tsender=\n\tfrom=\n\tsubject=\n\tlayout=default\n\toption=com_mailto\n\ttask=send\n\ttmpl=component\n\tlink=ef66622a24bd21112778f6a125d7c88ec019f726\n\t106c00969615486fcd9e1292cafd3f45=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t086279008dea642c05cc7e9d950687ba=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tb36b747818d0456fc33680274c9c2b93=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tcb516ffa12f2c86b3fae11fc4e555be2=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tebb20981ae03e49be82b96dc6b48ed2f=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t815b22ebb03a7660ec2af5b92f304d77=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\tcbd985a3d7c6e47528e499e71e9ef64b=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t65df56590c78b9bd45c2393eb285624c=1\n\nForm: http://tehnologiyaklimata.com/\n\tsearchword=поиск...\n\tsearchphrase=any\n\tlimit=\n\tordering=newest\n\tview=search\n\tItemid=99999999\n\toption=com_search\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t4fb984f9175a0daee7405e1c42bec681=1\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t054798a3e6c0e2beb580079e0fd2d3bc=1\n\nForm: \n\tname=Имя...\n\temail=Email...\n\tsubject=Тема...\n\tverif_box=Введите код 8340\n\temail_address=\n\nForm: /index.php/component/users/\n\tjform[name]=\n\tjform[username]=\n\tjform[email1]=\n\tjform[email2]=\n\tjform[password1]=\n\tjform[password2]=\n\toption=com_users\n\ttask=registration.register\n\t4549fbbfbceef3f8d7b17e1e0feece82=1\n\n	0381473e93281b72e2fa213ac88cbb5080ccdb3ca3bc566cdceace0a21f10e5a	\N	2013-06-03 05:26:30	\N	finished	2ca19d46b480540ec2fdf9b413a6948cd6f0467e173396653b5963b75e701477	1	\N	\N	tehnologiyaklimata.com	1	\N
1	42	renegotiation.py\n----------------\n* Session Renegotiation :\nClient-initiated Renegotiations:    Honored\nSecure Renegotiation:               Supported\n	cc878af5a35d5bb54b1d739c2d9f2a3e97a9ea6a400719709c9161d330c51683	\N	2013-06-05 02:22:05	\N	finished	16e4f0ddd472126f492365186d4a0f8ab90648b878508a945a24b95a9485d38c	1	\N	\N	84.253.25.172	1	\N
1	26	ssl_quality.py\n--------------\nSSL 2.0: No\nSSL 3.0: Yes\nTLS 1.0: Yes\nTLS 1.1: No\nTLS 1.2: No\n\nssl_quality.py\n--------------\nSSL 2.0: No\nSSL 3.0: Yes\nTLS 1.0: Yes\nTLS 1.1: Yes\nTLS 1.2: Yes\n	888cad671a1716515136f6e700e2153513d86b2ecae1e8a0f7790db19395428b	\N	2013-06-06 00:23:22	\N	finished	99b3b526d9a7570b75fc129b52a4972d14b3179788340d0ea0395c63a518392f	1	\N	\N	google.com	1	\N
1	12	Nameserver                     IP                  SOA Serial      Refresh    Retry      Expire     Minimum\n-----------------------------------------------------------------------------------------------------------\nns3.google.com                 IP 216.239.36.10    SOA 2013015000  2h         30m        14d        5m        \nns4.google.com                 IP 216.239.38.10    SOA 2013015000  2h         30m        14d        5m        \nns1.google.com                 IP 216.239.32.10    SOA 2013015000  2h         30m        14d        5m        \nns2.google.com                 IP 216.239.34.10    SOA 2013015000  2h         30m        14d        5m        \nThe recommended syntax for serial number is YYYYMMDDnn (YYYY=year, MM=month, DD=day, nn=revision number), RFC 1912 (2013015000)\nThe recommended value for minimum TTL is 1 to 5 days, RFC 1912 (5m)\n\ndns_soa.py\n----------\nTypeError: main() takes exactly 2 arguments (1 given)\n\ndns_soa.py\n----------\nNameserver                     IP                  SOA Serial      Refresh    Retry      Expire     Minimum\n-----------------------------------------------------------------------------------------------------------\ndns3.swisscom.com              IP 193.222.76.54    SOA 990         6h         60m        7d         10m        (ValueError: invalid literal for int() with base 10: '')\ndns1.swisscom.com              IP 138.190.34.196   SOA 990         6h         60m        7d         10m        (ValueError: invalid literal for int() with base 10: '')\ndns2.swisscom.com              IP 138.190.34.204   SOA 990         6h         60m        7d         10m        (ValueError: invalid literal for int() with base 10: '')\n\ndns_soa.py\n----------\n990\nkkk\n990\nkkk\n990\nkkk\nNameserver                     IP                  SOA Serial      Refresh    Retry      Expire     Minimum\n-----------------------------------------------------------------------------------------------------------\ndns1.swisscom.com              IP 138.190.34.196   SOA 990         6h         60m        7d         10m        (ValueError: invalid literal for int() with base 10: '')\ndns2.swisscom.com              IP 138.190.34.204   SOA 990         6h         60m        7d         10m        (ValueError: invalid literal for int() with base 10: '')\ndns3.swisscom.com              IP 193.222.76.54    SOA 990         6h         60m        7d         10m        (ValueError: invalid literal for int() with base 10: '')\n\ndns_soa.py\n----------\nNameserver                     IP                  SOA Serial      Refresh    Retry      Expire     Minimum\n-----------------------------------------------------------------------------------------------------------\ndns2.swisscom.com              IP 138.190.34.204   SOA 990         6h         60m        7d         10m       \ndns3.swisscom.com              IP 193.222.76.54    SOA 990         6h         60m        7d         10m       \ndns1.swisscom.com              IP 138.190.34.196   SOA 990         6h         60m        7d         10m       \nThe recommended syntax for serial number is YYYYMMDDnn (YYYY=year, MM=month, DD=day, nn=revision number), RFC 1912 (990)\nThe recommended value for expire time is 2 to 4 weeks, RFC 1912 (7d)\nThe recommended value for minimum TTL is 1 to 5 days, RFC 1912 (10m)\n	67c9dbcfaac978abd986874f20eccaf46bd56ad7fd8523b495d8223f3788ce61	\N	2013-06-05 02:17:22	\N	finished	131f98d52ff1a9d2fb7ce9ae2d0348ddbf2adf24241d097876b70020a034b293	1	\N	\N	finma.ch	1	\N
\.


--
-- Data for Name: target_references; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_references (target_id, reference_id) FROM stdin;
1	1
2	1
3	1
4	1
6	1
7	1
\.


--
-- Data for Name: targets; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY targets (id, project_id, host, description) FROM stdin;
3	1	empty.com	\N
4	2	127.0.0.1	\N
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
1	erbol.turburgaev@gmail.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3	Oliver Muenchow	\N	admin	2013-07-08 17:02:20	t	\N	2013-05-24 16:02:29.309728	f	f	f	FDC71CACAFD354F2	/C=CH/ST=Zurich/L=Zurich/O=GTTA/CN=GTTA
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
-- Name: project_gt_check_vulns_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_gt_check_vulns
    ADD CONSTRAINT project_gt_check_vulns_pkey PRIMARY KEY (project_id, gt_check_id);


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
-- Name: gt_checks_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX gt_checks_id_key ON gt_checks USING btree (id);


--
-- Name: gt_dependency_processors_id_key; Type: INDEX; Schema: public; Owner: gtta; Tablespace: 
--

CREATE UNIQUE INDEX gt_dependency_processors_id_key ON gt_dependency_processors USING btree (id);


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
-- Name: project_gt_check_vulns_gt_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_check_vulns
    ADD CONSTRAINT project_gt_check_vulns_gt_check_id_fkey FOREIGN KEY (gt_check_id) REFERENCES gt_checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_check_vulns_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_check_vulns
    ADD CONSTRAINT project_gt_check_vulns_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: project_gt_check_vulns_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_gt_check_vulns
    ADD CONSTRAINT project_gt_check_vulns_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


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

