--
-- PostgreSQL database dump
--

-- Dumped from database version 8.4.12
-- Dumped by pg_dump version 9.1.3
-- Started on 2012-07-21 02:14:27 MSK

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- TOC entry 475 (class 1247 OID 16387)
-- Dependencies: 6
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
-- TOC entry 478 (class 1247 OID 16394)
-- Dependencies: 6
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
-- TOC entry 481 (class 1247 OID 16400)
-- Dependencies: 6
-- Name: project_status; Type: TYPE; Schema: public; Owner: gtta
--

CREATE TYPE project_status AS ENUM (
    'open',
    'in_progress',
    'finished'
);


ALTER TYPE public.project_status OWNER TO gtta;

--
-- TOC entry 484 (class 1247 OID 16405)
-- Dependencies: 6
-- Name: user_role; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE user_role AS ENUM (
    'admin',
    'user',
    'client'
);


ALTER TYPE public.user_role OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 140 (class 1259 OID 16409)
-- Dependencies: 6
-- Name: check_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_categories (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL
);


ALTER TABLE public.check_categories OWNER TO gtta;

--
-- TOC entry 141 (class 1259 OID 16415)
-- Dependencies: 140 6
-- Name: check_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.check_categories_id_seq OWNER TO gtta;

--
-- TOC entry 2088 (class 0 OID 0)
-- Dependencies: 141
-- Name: check_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_categories_id_seq OWNED BY check_categories.id;


--
-- TOC entry 2089 (class 0 OID 0)
-- Dependencies: 141
-- Name: check_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_categories_id_seq', 19, true);


--
-- TOC entry 142 (class 1259 OID 16417)
-- Dependencies: 6
-- Name: check_categories_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_categories_l10n (
    check_category_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.check_categories_l10n OWNER TO gtta;

--
-- TOC entry 175 (class 1259 OID 16951)
-- Dependencies: 6
-- Name: check_controls; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_controls (
    id bigint NOT NULL,
    check_category_id bigint NOT NULL,
    name character varying(1000) NOT NULL
);


ALTER TABLE public.check_controls OWNER TO gtta;

--
-- TOC entry 174 (class 1259 OID 16949)
-- Dependencies: 6 175
-- Name: check_controls_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_controls_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.check_controls_id_seq OWNER TO gtta;

--
-- TOC entry 2090 (class 0 OID 0)
-- Dependencies: 174
-- Name: check_controls_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_controls_id_seq OWNED BY check_controls.id;


--
-- TOC entry 2091 (class 0 OID 0)
-- Dependencies: 174
-- Name: check_controls_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_controls_id_seq', 3, true);


--
-- TOC entry 176 (class 1259 OID 16965)
-- Dependencies: 6
-- Name: check_controls_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_controls_l10n (
    check_control_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.check_controls_l10n OWNER TO gtta;

--
-- TOC entry 143 (class 1259 OID 16423)
-- Dependencies: 1933 6
-- Name: check_inputs; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_inputs (
    id bigint NOT NULL,
    check_id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    description character varying,
    sort_order integer DEFAULT 0 NOT NULL,
    value character varying
);


ALTER TABLE public.check_inputs OWNER TO gtta;

--
-- TOC entry 144 (class 1259 OID 16430)
-- Dependencies: 6 143
-- Name: check_inputs_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_inputs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.check_inputs_id_seq OWNER TO gtta;

--
-- TOC entry 2092 (class 0 OID 0)
-- Dependencies: 144
-- Name: check_inputs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_inputs_id_seq OWNED BY check_inputs.id;


--
-- TOC entry 2093 (class 0 OID 0)
-- Dependencies: 144
-- Name: check_inputs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_inputs_id_seq', 53, true);


--
-- TOC entry 145 (class 1259 OID 16432)
-- Dependencies: 6
-- Name: check_inputs_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_inputs_l10n (
    check_input_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000),
    description character varying,
    value character varying
);


ALTER TABLE public.check_inputs_l10n OWNER TO gtta;

--
-- TOC entry 146 (class 1259 OID 16438)
-- Dependencies: 1935 6
-- Name: check_results; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_results (
    id bigint NOT NULL,
    check_id bigint NOT NULL,
    result character varying NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.check_results OWNER TO gtta;

--
-- TOC entry 147 (class 1259 OID 16445)
-- Dependencies: 146 6
-- Name: check_results_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_results_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.check_results_id_seq OWNER TO gtta;

--
-- TOC entry 2094 (class 0 OID 0)
-- Dependencies: 147
-- Name: check_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_results_id_seq OWNED BY check_results.id;


--
-- TOC entry 2095 (class 0 OID 0)
-- Dependencies: 147
-- Name: check_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_results_id_seq', 12, true);


--
-- TOC entry 148 (class 1259 OID 16447)
-- Dependencies: 6
-- Name: check_results_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_results_l10n (
    check_result_id bigint NOT NULL,
    language_id bigint NOT NULL,
    result character varying
);


ALTER TABLE public.check_results_l10n OWNER TO gtta;

--
-- TOC entry 149 (class 1259 OID 16453)
-- Dependencies: 1937 6
-- Name: check_solutions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_solutions (
    id bigint NOT NULL,
    check_id bigint NOT NULL,
    solution character varying NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.check_solutions OWNER TO gtta;

--
-- TOC entry 150 (class 1259 OID 16460)
-- Dependencies: 149 6
-- Name: check_solutions_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE check_solutions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.check_solutions_id_seq OWNER TO gtta;

--
-- TOC entry 2096 (class 0 OID 0)
-- Dependencies: 150
-- Name: check_solutions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_solutions_id_seq OWNED BY check_solutions.id;


--
-- TOC entry 2097 (class 0 OID 0)
-- Dependencies: 150
-- Name: check_solutions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_solutions_id_seq', 20, true);


--
-- TOC entry 151 (class 1259 OID 16462)
-- Dependencies: 6
-- Name: check_solutions_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_solutions_l10n (
    check_solution_id bigint NOT NULL,
    language_id bigint NOT NULL,
    solution character varying
);


ALTER TABLE public.check_solutions_l10n OWNER TO gtta;

--
-- TOC entry 152 (class 1259 OID 16468)
-- Dependencies: 6
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
    script character varying(1000),
    multiple_solutions boolean NOT NULL,
    protocol character varying(1000),
    port integer,
    reference character varying,
    question character varying,
    reference_id bigint NOT NULL,
    reference_code character varying(1000),
    reference_url character varying(1000)
);


ALTER TABLE public.checks OWNER TO gtta;

--
-- TOC entry 153 (class 1259 OID 16474)
-- Dependencies: 152 6
-- Name: checks_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE checks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.checks_id_seq OWNER TO gtta;

--
-- TOC entry 2098 (class 0 OID 0)
-- Dependencies: 153
-- Name: checks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE checks_id_seq OWNED BY checks.id;


--
-- TOC entry 2099 (class 0 OID 0)
-- Dependencies: 153
-- Name: checks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('checks_id_seq', 67, true);


--
-- TOC entry 154 (class 1259 OID 16476)
-- Dependencies: 6
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
-- TOC entry 155 (class 1259 OID 16482)
-- Dependencies: 6
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
    contact_email character varying(1000)
);


ALTER TABLE public.clients OWNER TO gtta;

--
-- TOC entry 156 (class 1259 OID 16488)
-- Dependencies: 6 155
-- Name: clients_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.clients_id_seq OWNER TO gtta;

--
-- TOC entry 2100 (class 0 OID 0)
-- Dependencies: 156
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE clients_id_seq OWNED BY clients.id;


--
-- TOC entry 2101 (class 0 OID 0)
-- Dependencies: 156
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('clients_id_seq', 15, true);


--
-- TOC entry 173 (class 1259 OID 16903)
-- Dependencies: 1956 1957 6
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
-- TOC entry 172 (class 1259 OID 16901)
-- Dependencies: 6 173
-- Name: emails_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE emails_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.emails_id_seq OWNER TO gtta;

--
-- TOC entry 2102 (class 0 OID 0)
-- Dependencies: 172
-- Name: emails_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE emails_id_seq OWNED BY emails.id;


--
-- TOC entry 2103 (class 0 OID 0)
-- Dependencies: 172
-- Name: emails_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('emails_id_seq', 12, true);


--
-- TOC entry 157 (class 1259 OID 16490)
-- Dependencies: 6
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
-- TOC entry 158 (class 1259 OID 16496)
-- Dependencies: 157 6
-- Name: languages_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE languages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.languages_id_seq OWNER TO gtta;

--
-- TOC entry 2104 (class 0 OID 0)
-- Dependencies: 158
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE languages_id_seq OWNED BY languages.id;


--
-- TOC entry 2105 (class 0 OID 0)
-- Dependencies: 158
-- Name: languages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('languages_id_seq', 4, true);


--
-- TOC entry 159 (class 1259 OID 16498)
-- Dependencies: 6
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
-- TOC entry 160 (class 1259 OID 16504)
-- Dependencies: 159 6
-- Name: project_details_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE project_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_details_id_seq OWNER TO gtta;

--
-- TOC entry 2106 (class 0 OID 0)
-- Dependencies: 160
-- Name: project_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE project_details_id_seq OWNED BY project_details.id;


--
-- TOC entry 2107 (class 0 OID 0)
-- Dependencies: 160
-- Name: project_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('project_details_id_seq', 5, true);


--
-- TOC entry 161 (class 1259 OID 16506)
-- Dependencies: 1943 481 6
-- Name: projects; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE projects (
    id bigint NOT NULL,
    client_id bigint NOT NULL,
    year character(4) NOT NULL,
    deadline date NOT NULL,
    name character varying(1000) NOT NULL,
    status project_status DEFAULT 'open'::project_status NOT NULL
);


ALTER TABLE public.projects OWNER TO gtta;

--
-- TOC entry 162 (class 1259 OID 16513)
-- Dependencies: 6 161
-- Name: projects_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE projects_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.projects_id_seq OWNER TO gtta;

--
-- TOC entry 2108 (class 0 OID 0)
-- Dependencies: 162
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE projects_id_seq OWNED BY projects.id;


--
-- TOC entry 2109 (class 0 OID 0)
-- Dependencies: 162
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('projects_id_seq', 14, true);


--
-- TOC entry 178 (class 1259 OID 17015)
-- Dependencies: 6
-- Name: references; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE "references" (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL,
    url character varying(1000)
);


ALTER TABLE public."references" OWNER TO gtta;

--
-- TOC entry 177 (class 1259 OID 17013)
-- Dependencies: 6 178
-- Name: references_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE references_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.references_id_seq OWNER TO gtta;

--
-- TOC entry 2110 (class 0 OID 0)
-- Dependencies: 177
-- Name: references_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE references_id_seq OWNED BY "references".id;


--
-- TOC entry 2111 (class 0 OID 0)
-- Dependencies: 177
-- Name: references_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('references_id_seq', 5, true);


--
-- TOC entry 163 (class 1259 OID 16515)
-- Dependencies: 1945 6
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
-- TOC entry 164 (class 1259 OID 16522)
-- Dependencies: 1946 1947 1948 1949 1950 6
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
    high_risk_count bigint DEFAULT 0 NOT NULL
);


ALTER TABLE public.target_check_categories OWNER TO gtta;

--
-- TOC entry 165 (class 1259 OID 16530)
-- Dependencies: 6
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
-- TOC entry 166 (class 1259 OID 16536)
-- Dependencies: 6
-- Name: target_check_solutions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_check_solutions (
    target_id bigint NOT NULL,
    check_solution_id bigint NOT NULL,
    check_id bigint NOT NULL
);


ALTER TABLE public.target_check_solutions OWNER TO gtta;

--
-- TOC entry 167 (class 1259 OID 16539)
-- Dependencies: 1951 6 478 475
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
    user_id bigint NOT NULL
);


ALTER TABLE public.target_checks OWNER TO gtta;

--
-- TOC entry 179 (class 1259 OID 17034)
-- Dependencies: 6
-- Name: target_references; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_references (
    target_id bigint NOT NULL,
    reference_id bigint NOT NULL
);


ALTER TABLE public.target_references OWNER TO gtta;

--
-- TOC entry 168 (class 1259 OID 16546)
-- Dependencies: 6
-- Name: targets; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE targets (
    id bigint NOT NULL,
    project_id bigint NOT NULL,
    host character varying(1000) NOT NULL
);


ALTER TABLE public.targets OWNER TO gtta;

--
-- TOC entry 169 (class 1259 OID 16552)
-- Dependencies: 6 168
-- Name: targets_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE targets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.targets_id_seq OWNER TO gtta;

--
-- TOC entry 2112 (class 0 OID 0)
-- Dependencies: 169
-- Name: targets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE targets_id_seq OWNED BY targets.id;


--
-- TOC entry 2113 (class 0 OID 0)
-- Dependencies: 169
-- Name: targets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('targets_id_seq', 23, true);


--
-- TOC entry 170 (class 1259 OID 16554)
-- Dependencies: 1953 484 6
-- Name: users; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE users (
    id bigint NOT NULL,
    email character varying(1000) NOT NULL,
    password character varying(1000) NOT NULL,
    name character varying(1000),
    client_id bigint,
    role user_role DEFAULT 'admin'::user_role NOT NULL
);


ALTER TABLE public.users OWNER TO gtta;

--
-- TOC entry 171 (class 1259 OID 16561)
-- Dependencies: 6 170
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO gtta;

--
-- TOC entry 2114 (class 0 OID 0)
-- Dependencies: 171
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- TOC entry 2115 (class 0 OID 0)
-- Dependencies: 171
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('users_id_seq', 16, true);


--
-- TOC entry 1932 (class 2604 OID 16563)
-- Dependencies: 141 140
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories ALTER COLUMN id SET DEFAULT nextval('check_categories_id_seq'::regclass);


--
-- TOC entry 1958 (class 2604 OID 16954)
-- Dependencies: 175 174 175
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls ALTER COLUMN id SET DEFAULT nextval('check_controls_id_seq'::regclass);


--
-- TOC entry 1934 (class 2604 OID 16564)
-- Dependencies: 144 143
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs ALTER COLUMN id SET DEFAULT nextval('check_inputs_id_seq'::regclass);


--
-- TOC entry 1936 (class 2604 OID 16565)
-- Dependencies: 147 146
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results ALTER COLUMN id SET DEFAULT nextval('check_results_id_seq'::regclass);


--
-- TOC entry 1938 (class 2604 OID 16566)
-- Dependencies: 150 149
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions ALTER COLUMN id SET DEFAULT nextval('check_solutions_id_seq'::regclass);


--
-- TOC entry 1939 (class 2604 OID 16567)
-- Dependencies: 153 152
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks ALTER COLUMN id SET DEFAULT nextval('checks_id_seq'::regclass);


--
-- TOC entry 1940 (class 2604 OID 16568)
-- Dependencies: 156 155
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY clients ALTER COLUMN id SET DEFAULT nextval('clients_id_seq'::regclass);


--
-- TOC entry 1955 (class 2604 OID 16906)
-- Dependencies: 173 172 173
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails ALTER COLUMN id SET DEFAULT nextval('emails_id_seq'::regclass);


--
-- TOC entry 1941 (class 2604 OID 16569)
-- Dependencies: 158 157
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY languages ALTER COLUMN id SET DEFAULT nextval('languages_id_seq'::regclass);


--
-- TOC entry 1942 (class 2604 OID 16570)
-- Dependencies: 160 159
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details ALTER COLUMN id SET DEFAULT nextval('project_details_id_seq'::regclass);


--
-- TOC entry 1944 (class 2604 OID 16571)
-- Dependencies: 162 161
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects ALTER COLUMN id SET DEFAULT nextval('projects_id_seq'::regclass);


--
-- TOC entry 1959 (class 2604 OID 17018)
-- Dependencies: 177 178 178
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY "references" ALTER COLUMN id SET DEFAULT nextval('references_id_seq'::regclass);


--
-- TOC entry 1952 (class 2604 OID 16572)
-- Dependencies: 169 168
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets ALTER COLUMN id SET DEFAULT nextval('targets_id_seq'::regclass);


--
-- TOC entry 1954 (class 2604 OID 16573)
-- Dependencies: 171 170
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- TOC entry 2057 (class 0 OID 16409)
-- Dependencies: 140
-- Data for Name: check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_categories (id, name) FROM stdin;
3	DNS
14	SMTP
15	TCP
16	Web
17	FTP
18	SSH
\.


--
-- TOC entry 2058 (class 0 OID 16417)
-- Dependencies: 142
-- Data for Name: check_categories_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_categories_l10n (check_category_id, language_id, name) FROM stdin;
3	3	DNS
3	4	DNS
14	3	SMTP
14	4	
15	3	TCP
15	4	
16	3	Web
16	4	
17	3	FTP
17	4	
18	3	SSH
18	4	
\.


--
-- TOC entry 2079 (class 0 OID 16951)
-- Dependencies: 175
-- Data for Name: check_controls; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_controls (id, check_category_id, name) FROM stdin;
2	3	Sigma Ex
3	3	Test Control
\.


--
-- TOC entry 2080 (class 0 OID 16965)
-- Dependencies: 176
-- Data for Name: check_controls_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_controls_l10n (check_control_id, language_id, name) FROM stdin;
2	3	Sigma Ex
2	4	Signe
3	3	Test Control
3	4	\N
\.


--
-- TOC entry 2059 (class 0 OID 16423)
-- Dependencies: 143
-- Data for Name: check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs (id, check_id, name, description, sort_order, value) FROM stdin;
\.


--
-- TOC entry 2060 (class 0 OID 16432)
-- Dependencies: 145
-- Data for Name: check_inputs_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs_l10n (check_input_id, language_id, name, description, value) FROM stdin;
\.


--
-- TOC entry 2061 (class 0 OID 16438)
-- Dependencies: 146
-- Data for Name: check_results; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results (id, check_id, result, sort_order) FROM stdin;
11	65	Hello, world!	0
\.


--
-- TOC entry 2062 (class 0 OID 16447)
-- Dependencies: 148
-- Data for Name: check_results_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results_l10n (check_result_id, language_id, result) FROM stdin;
11	3	Hello, world!
11	4	\N
\.


--
-- TOC entry 2063 (class 0 OID 16453)
-- Dependencies: 149
-- Data for Name: check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions (id, check_id, solution, sort_order) FROM stdin;
\.


--
-- TOC entry 2064 (class 0 OID 16462)
-- Dependencies: 151
-- Data for Name: check_solutions_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions_l10n (check_solution_id, language_id, solution) FROM stdin;
\.


--
-- TOC entry 2065 (class 0 OID 16468)
-- Dependencies: 152
-- Data for Name: checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks (id, check_control_id, name, background_info, hints, advanced, automated, script, multiple_solutions, protocol, port, reference, question, reference_id, reference_code, reference_url) FROM stdin;
67	3	DNS SPF			t	t	dns_spf.py	f		\N			4		
65	2	DNS Z			f	t	dns_a.py	f		\N			5	102	
66	2	DNS AFXR			f	t	dns_afxr.pl	f		\N			3	101	http://google.com
\.


--
-- TOC entry 2066 (class 0 OID 16476)
-- Dependencies: 154
-- Data for Name: checks_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks_l10n (check_id, language_id, name, background_info, hints, reference, question) FROM stdin;
67	3	DNS SPF	\N	\N	\N	\N
67	4	\N	\N	\N	\N	\N
65	3	DNS Z	\N	\N	\N	\N
65	4	\N	\N	\N	\N	\N
66	3	DNS AFXR	\N	\N	\N	\N
66	4	\N	\N	\N	\N	\N
\.


--
-- TOC entry 2067 (class 0 OID 16482)
-- Dependencies: 155
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY clients (id, name, country, state, city, address, postcode, website, contact_name, contact_phone, contact_email) FROM stdin;
15	Keke									
\.


--
-- TOC entry 2078 (class 0 OID 16903)
-- Dependencies: 173
-- Data for Name: emails; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY emails (id, user_id, subject, content, attempts, sent) FROM stdin;
\.


--
-- TOC entry 2068 (class 0 OID 16490)
-- Dependencies: 157
-- Data for Name: languages; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY languages (id, name, code, "default") FROM stdin;
3	English	en	t
4	Deutsch	de	f
\.


--
-- TOC entry 2069 (class 0 OID 16498)
-- Dependencies: 159
-- Data for Name: project_details; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_details (id, project_id, subject, content) FROM stdin;
\.


--
-- TOC entry 2070 (class 0 OID 16506)
-- Dependencies: 161
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY projects (id, client_id, year, deadline, name, status) FROM stdin;
13	15	2012	2012-07-08	Yay	in_progress
14	15	2013	2012-07-28	Yay 2	in_progress
\.


--
-- TOC entry 2081 (class 0 OID 17015)
-- Dependencies: 178
-- Data for Name: references; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY "references" (id, name, url) FROM stdin;
3	Hello	
4	Zilli	
5	Willi	
\.


--
-- TOC entry 2071 (class 0 OID 16515)
-- Dependencies: 163
-- Data for Name: target_check_attachments; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_attachments (target_id, check_id, name, type, path, size) FROM stdin;
\.


--
-- TOC entry 2072 (class 0 OID 16522)
-- Dependencies: 164
-- Data for Name: target_check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_categories (target_id, check_category_id, advanced, check_count, finished_count, low_risk_count, med_risk_count, high_risk_count) FROM stdin;
19	14	t	0	0	0	0	0
23	17	t	0	0	0	0	0
20	17	t	0	0	0	0	0
20	14	t	0	0	0	0	0
20	18	t	0	0	0	0	0
20	15	t	0	0	0	0	0
19	3	t	0	0	0	0	0
20	3	t	3	3	2	0	1
\.


--
-- TOC entry 2073 (class 0 OID 16530)
-- Dependencies: 165
-- Data for Name: target_check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_inputs (target_id, check_input_id, value, file, check_id) FROM stdin;
\.


--
-- TOC entry 2074 (class 0 OID 16536)
-- Dependencies: 166
-- Data for Name: target_check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_solutions (target_id, check_solution_id, check_id) FROM stdin;
\.


--
-- TOC entry 2075 (class 0 OID 16539)
-- Dependencies: 167
-- Data for Name: target_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_checks (target_id, check_id, result, target_file, rating, started, pid, status, result_file, language_id, protocol, port, override_target, user_id) FROM stdin;
20	66	\N	\N	high_risk	\N	\N	finished	\N	3	\N	\N	\N	1
20	65	\N	\N	low_risk	\N	\N	finished	\N	3	\N	\N	\N	1
20	67	\N	\N	low_risk	\N	\N	finished	\N	3	\N	\N	\N	1
19	65	Property "Check.check_category_id" is not defined.	d535e66ccb6ba02c99d761a5d359c5c051a27cd35d7ba4b399f203a4b5ba38d1	\N	2012-07-19 05:02:44.727995	\N	finished	80ad784ca04b07baf2c16fe73dacc2c20108ad2e0c6932d603b86b755004f83e	3	\N	\N	\N	1
\.


--
-- TOC entry 2082 (class 0 OID 17034)
-- Dependencies: 179
-- Data for Name: target_references; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_references (target_id, reference_id) FROM stdin;
20	4
20	3
20	5
\.


--
-- TOC entry 2076 (class 0 OID 16546)
-- Dependencies: 168
-- Data for Name: targets; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY targets (id, project_id, host) FROM stdin;
23	14	127.0.0.1
18	13	onexchanger.com
22	13	test.com
21	14	test.com
20	14	onexchanger.com
19	13	ziliboba.com
\.


--
-- TOC entry 2077 (class 0 OID 16554)
-- Dependencies: 170
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY users (id, email, password, name, client_id, role) FROM stdin;
2	test@user.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		\N	user
1	erbol.turburgaev@gmail.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3	John Doe	\N	admin
16	test@client.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		15	client
\.


--
-- TOC entry 1963 (class 2606 OID 16575)
-- Dependencies: 142 142 142
-- Name: check_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_pkey PRIMARY KEY (check_category_id, language_id);


--
-- TOC entry 1961 (class 2606 OID 16577)
-- Dependencies: 140 140
-- Name: check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories
    ADD CONSTRAINT check_categories_pkey PRIMARY KEY (id);


--
-- TOC entry 2013 (class 2606 OID 16972)
-- Dependencies: 176 176 176
-- Name: check_controls_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_pkey PRIMARY KEY (check_control_id, language_id);


--
-- TOC entry 2011 (class 2606 OID 16959)
-- Dependencies: 175 175
-- Name: check_controls_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_controls
    ADD CONSTRAINT check_controls_pkey PRIMARY KEY (id);


--
-- TOC entry 1967 (class 2606 OID 16579)
-- Dependencies: 145 145 145
-- Name: check_inputs_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_pkey PRIMARY KEY (check_input_id, language_id);


--
-- TOC entry 1965 (class 2606 OID 16581)
-- Dependencies: 143 143
-- Name: check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_pkey PRIMARY KEY (id);


--
-- TOC entry 1971 (class 2606 OID 16583)
-- Dependencies: 148 148 148
-- Name: check_results_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_pkey PRIMARY KEY (check_result_id, language_id);


--
-- TOC entry 1969 (class 2606 OID 16585)
-- Dependencies: 146 146
-- Name: check_results_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_pkey PRIMARY KEY (id);


--
-- TOC entry 1975 (class 2606 OID 16587)
-- Dependencies: 151 151 151
-- Name: check_solutions_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_pkey PRIMARY KEY (check_solution_id, language_id);


--
-- TOC entry 1973 (class 2606 OID 16589)
-- Dependencies: 149 149
-- Name: check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_pkey PRIMARY KEY (id);


--
-- TOC entry 1979 (class 2606 OID 16591)
-- Dependencies: 154 154 154
-- Name: checks_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_pkey PRIMARY KEY (check_id, language_id);


--
-- TOC entry 1977 (class 2606 OID 16593)
-- Dependencies: 152 152
-- Name: checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_pkey PRIMARY KEY (id);


--
-- TOC entry 1981 (class 2606 OID 16595)
-- Dependencies: 155 155
-- Name: clients_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- TOC entry 2009 (class 2606 OID 16908)
-- Dependencies: 173 173
-- Name: emails_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_pkey PRIMARY KEY (id);


--
-- TOC entry 1983 (class 2606 OID 16597)
-- Dependencies: 157 157
-- Name: languages_code_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_code_key UNIQUE (code);


--
-- TOC entry 1985 (class 2606 OID 16599)
-- Dependencies: 157 157
-- Name: languages_name_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_name_key UNIQUE (name);


--
-- TOC entry 1987 (class 2606 OID 16601)
-- Dependencies: 157 157
-- Name: languages_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- TOC entry 1989 (class 2606 OID 16603)
-- Dependencies: 159 159
-- Name: project_details_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_pkey PRIMARY KEY (id);


--
-- TOC entry 1991 (class 2606 OID 16605)
-- Dependencies: 161 161
-- Name: projects_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- TOC entry 2015 (class 2606 OID 17023)
-- Dependencies: 178 178
-- Name: references_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY "references"
    ADD CONSTRAINT references_pkey PRIMARY KEY (id);


--
-- TOC entry 1993 (class 2606 OID 16607)
-- Dependencies: 163 163
-- Name: target_check_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_pkey PRIMARY KEY (path);


--
-- TOC entry 1995 (class 2606 OID 16609)
-- Dependencies: 164 164 164
-- Name: target_check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_pkey PRIMARY KEY (target_id, check_category_id);


--
-- TOC entry 1997 (class 2606 OID 16611)
-- Dependencies: 165 165 165
-- Name: target_check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_pkey PRIMARY KEY (target_id, check_input_id);


--
-- TOC entry 1999 (class 2606 OID 16613)
-- Dependencies: 166 166 166
-- Name: target_check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_pkey PRIMARY KEY (target_id, check_solution_id);


--
-- TOC entry 2001 (class 2606 OID 16615)
-- Dependencies: 167 167 167
-- Name: target_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_pkey PRIMARY KEY (target_id, check_id);


--
-- TOC entry 2017 (class 2606 OID 17038)
-- Dependencies: 179 179 179
-- Name: target_references_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_pkey PRIMARY KEY (target_id, reference_id);


--
-- TOC entry 2003 (class 2606 OID 16617)
-- Dependencies: 168 168
-- Name: targets_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_pkey PRIMARY KEY (id);


--
-- TOC entry 2005 (class 2606 OID 16619)
-- Dependencies: 170 170
-- Name: users_email_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 2007 (class 2606 OID 16621)
-- Dependencies: 170 170
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 2018 (class 2606 OID 16622)
-- Dependencies: 140 142 1960
-- Name: check_categories_l10n_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2019 (class 2606 OID 16627)
-- Dependencies: 1986 142 157
-- Name: check_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2052 (class 2606 OID 16998)
-- Dependencies: 140 1960 175
-- Name: check_controls_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls
    ADD CONSTRAINT check_controls_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2053 (class 2606 OID 17003)
-- Dependencies: 176 175 2010
-- Name: check_controls_l10n_check_control_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_check_control_id_fkey FOREIGN KEY (check_control_id) REFERENCES check_controls(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2054 (class 2606 OID 17008)
-- Dependencies: 157 1986 176
-- Name: check_controls_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2020 (class 2606 OID 16632)
-- Dependencies: 152 143 1976
-- Name: check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2021 (class 2606 OID 16637)
-- Dependencies: 145 1964 143
-- Name: check_inputs_l10n_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2022 (class 2606 OID 16642)
-- Dependencies: 1986 145 157
-- Name: check_inputs_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2023 (class 2606 OID 16647)
-- Dependencies: 1976 146 152
-- Name: check_results_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2024 (class 2606 OID 16652)
-- Dependencies: 1968 148 146
-- Name: check_results_l10n_check_result_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_check_result_id_fkey FOREIGN KEY (check_result_id) REFERENCES check_results(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2025 (class 2606 OID 16657)
-- Dependencies: 148 1986 157
-- Name: check_results_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2026 (class 2606 OID 16662)
-- Dependencies: 152 1976 149
-- Name: check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2027 (class 2606 OID 16667)
-- Dependencies: 151 149 1972
-- Name: check_solutions_l10n_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2028 (class 2606 OID 16672)
-- Dependencies: 157 1986 151
-- Name: check_solutions_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2029 (class 2606 OID 17069)
-- Dependencies: 152 2010 175
-- Name: checks_check_control_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_check_control_id_fkey FOREIGN KEY (check_control_id) REFERENCES check_controls(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2031 (class 2606 OID 16856)
-- Dependencies: 152 154 1976
-- Name: checks_l10n_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2032 (class 2606 OID 16861)
-- Dependencies: 1986 157 154
-- Name: checks_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2030 (class 2606 OID 17074)
-- Dependencies: 178 152 2014
-- Name: checks_reference_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_reference_id_fkey FOREIGN KEY (reference_id) REFERENCES "references"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2051 (class 2606 OID 16944)
-- Dependencies: 2006 173 170
-- Name: emails_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2033 (class 2606 OID 16692)
-- Dependencies: 159 161 1990
-- Name: project_details_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2034 (class 2606 OID 16697)
-- Dependencies: 155 161 1980
-- Name: projects_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2035 (class 2606 OID 16702)
-- Dependencies: 163 152 1976
-- Name: target_check_attachments_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2036 (class 2606 OID 16707)
-- Dependencies: 2002 168 163
-- Name: target_check_attachments_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2037 (class 2606 OID 16712)
-- Dependencies: 140 164 1960
-- Name: target_check_categories_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2038 (class 2606 OID 16717)
-- Dependencies: 168 164 2002
-- Name: target_check_categories_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2041 (class 2606 OID 16993)
-- Dependencies: 1976 152 165
-- Name: target_check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2039 (class 2606 OID 16983)
-- Dependencies: 1964 165 143
-- Name: target_check_inputs_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2040 (class 2606 OID 16988)
-- Dependencies: 2002 165 168
-- Name: target_check_inputs_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2042 (class 2606 OID 16737)
-- Dependencies: 152 1976 166
-- Name: target_check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id);


--
-- TOC entry 2043 (class 2606 OID 16742)
-- Dependencies: 149 1972 166
-- Name: target_check_solutions_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2044 (class 2606 OID 16747)
-- Dependencies: 166 168 2002
-- Name: target_check_solutions_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2045 (class 2606 OID 16881)
-- Dependencies: 167 1976 152
-- Name: target_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2046 (class 2606 OID 16886)
-- Dependencies: 167 157 1986
-- Name: target_checks_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2047 (class 2606 OID 16891)
-- Dependencies: 167 2002 168
-- Name: target_checks_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2048 (class 2606 OID 16896)
-- Dependencies: 167 2006 170
-- Name: target_checks_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2056 (class 2606 OID 17044)
-- Dependencies: 178 2014 179
-- Name: target_references_reference_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_reference_id_fkey FOREIGN KEY (reference_id) REFERENCES "references"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2055 (class 2606 OID 17039)
-- Dependencies: 168 179 2002
-- Name: target_references_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2049 (class 2606 OID 16767)
-- Dependencies: 161 1990 168
-- Name: targets_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2050 (class 2606 OID 16772)
-- Dependencies: 155 1980 170
-- Name: users_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2087 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2012-07-21 02:14:27 MSK

--
-- PostgreSQL database dump complete
--

