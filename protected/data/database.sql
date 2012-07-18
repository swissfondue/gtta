--
-- PostgreSQL database dump
--

-- Dumped from database version 8.4.12
-- Dumped by pg_dump version 9.1.3
-- Started on 2012-07-18 23:02:42 MSK

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- TOC entry 469 (class 1247 OID 16387)
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
-- TOC entry 472 (class 1247 OID 16394)
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
-- TOC entry 475 (class 1247 OID 16400)
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
-- TOC entry 478 (class 1247 OID 16405)
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
-- Dependencies: 6 140
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
-- TOC entry 2045 (class 0 OID 0)
-- Dependencies: 141
-- Name: check_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_categories_id_seq OWNED BY check_categories.id;


--
-- TOC entry 2046 (class 0 OID 0)
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
-- TOC entry 143 (class 1259 OID 16423)
-- Dependencies: 1910 6
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
-- Dependencies: 143 6
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
-- TOC entry 2047 (class 0 OID 0)
-- Dependencies: 144
-- Name: check_inputs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_inputs_id_seq OWNED BY check_inputs.id;


--
-- TOC entry 2048 (class 0 OID 0)
-- Dependencies: 144
-- Name: check_inputs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_inputs_id_seq', 52, true);


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
-- Dependencies: 1912 6
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
-- Dependencies: 6 146
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
-- TOC entry 2049 (class 0 OID 0)
-- Dependencies: 147
-- Name: check_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_results_id_seq OWNED BY check_results.id;


--
-- TOC entry 2050 (class 0 OID 0)
-- Dependencies: 147
-- Name: check_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_results_id_seq', 10, true);


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
-- Dependencies: 1914 6
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
-- Dependencies: 6 149
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
-- TOC entry 2051 (class 0 OID 0)
-- Dependencies: 150
-- Name: check_solutions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_solutions_id_seq OWNED BY check_solutions.id;


--
-- TOC entry 2052 (class 0 OID 0)
-- Dependencies: 150
-- Name: check_solutions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_solutions_id_seq', 19, true);


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
    check_category_id bigint NOT NULL,
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
    question character varying
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
-- TOC entry 2053 (class 0 OID 0)
-- Dependencies: 153
-- Name: checks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE checks_id_seq OWNED BY checks.id;


--
-- TOC entry 2054 (class 0 OID 0)
-- Dependencies: 153
-- Name: checks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('checks_id_seq', 64, true);


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
-- TOC entry 2055 (class 0 OID 0)
-- Dependencies: 156
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE clients_id_seq OWNED BY clients.id;


--
-- TOC entry 2056 (class 0 OID 0)
-- Dependencies: 156
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('clients_id_seq', 15, true);


--
-- TOC entry 173 (class 1259 OID 16903)
-- Dependencies: 1933 1934 6
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
-- Dependencies: 173 6
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
-- TOC entry 2057 (class 0 OID 0)
-- Dependencies: 172
-- Name: emails_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE emails_id_seq OWNED BY emails.id;


--
-- TOC entry 2058 (class 0 OID 0)
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
-- Dependencies: 6 157
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
-- TOC entry 2059 (class 0 OID 0)
-- Dependencies: 158
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE languages_id_seq OWNED BY languages.id;


--
-- TOC entry 2060 (class 0 OID 0)
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
-- Dependencies: 6 159
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
-- TOC entry 2061 (class 0 OID 0)
-- Dependencies: 160
-- Name: project_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE project_details_id_seq OWNED BY project_details.id;


--
-- TOC entry 2062 (class 0 OID 0)
-- Dependencies: 160
-- Name: project_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('project_details_id_seq', 5, true);


--
-- TOC entry 161 (class 1259 OID 16506)
-- Dependencies: 1920 475 6
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
-- TOC entry 2063 (class 0 OID 0)
-- Dependencies: 162
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE projects_id_seq OWNED BY projects.id;


--
-- TOC entry 2064 (class 0 OID 0)
-- Dependencies: 162
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('projects_id_seq', 14, true);


--
-- TOC entry 163 (class 1259 OID 16515)
-- Dependencies: 1922 6
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
-- Dependencies: 1923 1924 1925 1926 1927 6
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
-- Dependencies: 1928 6 469 472
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
-- TOC entry 2065 (class 0 OID 0)
-- Dependencies: 169
-- Name: targets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE targets_id_seq OWNED BY targets.id;


--
-- TOC entry 2066 (class 0 OID 0)
-- Dependencies: 169
-- Name: targets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('targets_id_seq', 22, true);


--
-- TOC entry 170 (class 1259 OID 16554)
-- Dependencies: 1930 6 478
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
-- TOC entry 2067 (class 0 OID 0)
-- Dependencies: 171
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- TOC entry 2068 (class 0 OID 0)
-- Dependencies: 171
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('users_id_seq', 16, true);


--
-- TOC entry 1909 (class 2604 OID 16563)
-- Dependencies: 141 140
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories ALTER COLUMN id SET DEFAULT nextval('check_categories_id_seq'::regclass);


--
-- TOC entry 1911 (class 2604 OID 16564)
-- Dependencies: 144 143
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs ALTER COLUMN id SET DEFAULT nextval('check_inputs_id_seq'::regclass);


--
-- TOC entry 1913 (class 2604 OID 16565)
-- Dependencies: 147 146
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results ALTER COLUMN id SET DEFAULT nextval('check_results_id_seq'::regclass);


--
-- TOC entry 1915 (class 2604 OID 16566)
-- Dependencies: 150 149
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions ALTER COLUMN id SET DEFAULT nextval('check_solutions_id_seq'::regclass);


--
-- TOC entry 1916 (class 2604 OID 16567)
-- Dependencies: 153 152
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks ALTER COLUMN id SET DEFAULT nextval('checks_id_seq'::regclass);


--
-- TOC entry 1917 (class 2604 OID 16568)
-- Dependencies: 156 155
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY clients ALTER COLUMN id SET DEFAULT nextval('clients_id_seq'::regclass);


--
-- TOC entry 1932 (class 2604 OID 16906)
-- Dependencies: 173 172 173
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails ALTER COLUMN id SET DEFAULT nextval('emails_id_seq'::regclass);


--
-- TOC entry 1918 (class 2604 OID 16569)
-- Dependencies: 158 157
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY languages ALTER COLUMN id SET DEFAULT nextval('languages_id_seq'::regclass);


--
-- TOC entry 1919 (class 2604 OID 16570)
-- Dependencies: 160 159
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details ALTER COLUMN id SET DEFAULT nextval('project_details_id_seq'::regclass);


--
-- TOC entry 1921 (class 2604 OID 16571)
-- Dependencies: 162 161
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects ALTER COLUMN id SET DEFAULT nextval('projects_id_seq'::regclass);


--
-- TOC entry 1929 (class 2604 OID 16572)
-- Dependencies: 169 168
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets ALTER COLUMN id SET DEFAULT nextval('targets_id_seq'::regclass);


--
-- TOC entry 1931 (class 2604 OID 16573)
-- Dependencies: 171 170
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- TOC entry 2018 (class 0 OID 16409)
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
-- TOC entry 2019 (class 0 OID 16417)
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
-- TOC entry 2020 (class 0 OID 16423)
-- Dependencies: 143
-- Data for Name: check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs (id, check_id, name, description, sort_order, value) FROM stdin;
11	28	Recipient		0	
12	28	Server		1	
13	28	Login		2	
14	28	Password		3	
15	28	Sender		4	
16	28	Folder		5	
19	31	Page Type		2	php
20	31	Cookies		2	
21	31	URL Limit		3	100
23	22	Hostname		0	
25	24	Hostname		0	
26	25	Hostname		0	
8	23	Show All		0	0
27	32	Range Count		0	10
28	34	Timeout		0	120
29	34	Debug		1	0
30	35	Timeout		0	120
31	35	Debug		1	0
33	40	Users		0	
34	40	Passwords		1	
35	46	Code	Possible values: php, cfm, asp	0	php
36	47	Timeout	DNS timeout.	0	10
37	47	Max Results	Maximum number of results	1	100
38	47	Mode	Operation mode: 0 - output generated list only, 1 - resolve IP check	2	0
39	51	Port Range	2 lines: start and end of the range.	0	1\r\n80
40	52	Port Range	Port range that will be passed to nmap. Please use nmap syntax for -p command line argument (for example, 22; 1-65535; U:53,111,137,T:21-25,80,139,8080)	0	
41	52	Timeout	Timeout in milliseconds.	1	1000
42	53	Timeout	SMTP server connection timeout	0	10
44	53	Destination E-mail		2	destination@gmail.com
43	53	Source E-mail		1	source@gmail.com
45	54	Users		0	
46	54	Passwords		1	
32	39	Long List	0 - use small list, 1 - use long list.	0	0
47	55	Long List	0 - use small list, 1 - use long list.	0	
48	56	URLs	List of URLs.	0	
49	60	Timeout		0	10
50	61	Paths		0	
51	62	Paths		0	
52	63	Paths		0	
22	1	Hostname		0	
\.


--
-- TOC entry 2021 (class 0 OID 16432)
-- Dependencies: 145
-- Data for Name: check_inputs_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs_l10n (check_input_id, language_id, name, description, value) FROM stdin;
50	4			
51	3	Paths		
11	3	Recipient		
11	4			
12	3	Server		
12	4			
13	3	Login		
13	4			
14	3	Password		
14	4			
15	3	Sender		
15	4			
16	3	Folder		
16	4			
19	3	Page Type		php
19	4			
20	3	Cookies		
20	4			
21	3	URL Limit		100
21	4			
23	3	Hostname		
23	4			
25	3	Hostname		
25	4			
26	3	Hostname		
26	4			
8	3	Show All		0
8	4			
27	3	Range Count		10
27	4			
28	3	Timeout		120
28	4			
29	3	Debug		0
29	4			
30	3	Timeout		120
30	4			
31	3	Debug		0
31	4			
33	3	Users		
33	4			
34	3	Passwords		
34	4			
35	3	Code	Possible values: php, cfm, asp	php
35	4			
36	3	Timeout	DNS timeout.	10
36	4			
37	3	Max Results	Maximum number of results	100
37	4			
38	3	Mode	Operation mode: 0 - output generated list only, 1 - resolve IP check	0
38	4			
39	3	Port Range	2 lines: start and end of the range.	1\r\n80
39	4			
40	3	Port Range	Port range that will be passed to nmap. Please use nmap syntax for -p command line argument (for example, 22; 1-65535; U:53,111,137,T:21-25,80,139,8080)	
40	4			
41	3	Timeout	Timeout in milliseconds.	1000
41	4			
42	3	Timeout	SMTP server connection timeout	10
42	4			
44	3	Destination E-mail		destination@gmail.com
44	4			
43	3	Source E-mail		source@gmail.com
43	4			
45	3	Users		
45	4			
46	3	Passwords		
46	4			
32	3	Long List	0 - use small list, 1 - use long list.	0
32	4			
47	3	Long List	0 - use small list, 1 - use long list.	
47	4			
48	3	URLs	List of URLs.	
48	4			
49	3	Timeout		10
49	4			
50	3	Paths		
51	4			
52	3	Paths		
52	4			
22	3	Hostname	\N	\N
22	4	\N	\N	\N
\.


--
-- TOC entry 2022 (class 0 OID 16438)
-- Dependencies: 146
-- Data for Name: check_results; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results (id, check_id, result, sort_order) FROM stdin;
\.


--
-- TOC entry 2023 (class 0 OID 16447)
-- Dependencies: 148
-- Data for Name: check_results_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results_l10n (check_result_id, language_id, result) FROM stdin;
\.


--
-- TOC entry 2024 (class 0 OID 16453)
-- Dependencies: 149
-- Data for Name: check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions (id, check_id, solution, sort_order) FROM stdin;
13	22	Solution A	0
14	22	Solution B	0
15	25	Hello, world	0
16	25	Take away the color!	0
\.


--
-- TOC entry 2025 (class 0 OID 16462)
-- Dependencies: 151
-- Data for Name: check_solutions_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions_l10n (check_solution_id, language_id, solution) FROM stdin;
13	3	Solution A
13	4	
14	3	Solution B
14	4	
15	3	Hello, world
15	4	
16	3	Take away the color!
16	4	
\.


--
-- TOC entry 2026 (class 0 OID 16468)
-- Dependencies: 152
-- Data for Name: checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks (id, check_category_id, name, background_info, hints, advanced, automated, script, multiple_solutions, protocol, port, reference, question) FROM stdin;
33	3	DNS AFXR			t	t	dns_afxr.pl	f		\N		
34	3	DNS DOM MX			f	t	dns_dom_mx.pl	f		\N		
35	3	DNS Find NS			f	t	dns_find_ns.pl	f		\N		
23	3	DNS Hosting			f	t	dns_hosting.py	f	\N	\N	\N	\N
24	3	DNS SOA			f	t	dns_soa.py	f	\N	\N	\N	\N
25	3	DNS SPF			f	t	dns_spf.py	f	\N	\N	\N	\N
26	14	SMTP Banner			f	t	smtp_banner.py	f	\N	\N	\N	\N
27	14	SMTP DNSBL			f	t	smtp_dnsbl.py	f	\N	\N	\N	\N
28	14	SMTP Filter			f	t	smtp_filter.py	f	\N	\N	\N	\N
30	16	Web HTTP Methods			f	t	web_http_methods.py	f	\N	\N	\N	\N
31	16	Web SQL XSS			f	t	web_sql_xss.py	f	\N	\N	\N	\N
36	3	DNS IP Range			f	t	dns_ip_range.pl	f		\N		
47	3	DNS NIC Typosquatting			f	t	nic_typosquatting.pl	f		\N		
48	3	DNS NIC Whois			f	t	nic_whois.pl	f		\N		
29	15	TCP Traceroute			f	t	tcp_traceroute.py	f		80	\N	\N
50	3	DNS NS Version			f	t	ns_version.pl	f		\N		
37	3	DNS Resolve IP			f	t	dns_resolve_ip.pl	f		\N		
22	3	DNS A (Non-Recursive)			f	t	dns_a_nr.py	t		\N	\N	\N
40	17	FTP Bruteforce			f	t	ftp_bruteforce.pl	f		\N	\N	\N
1	3	DNS A			f	t	dns_a.py	f		\N		
54	18	SSH Bruteforce			f	t	ssh_bruteforce.pl	f		\N	\N	\N
38	3	DNS SPF (Perl)			f	t	dns_spf.pl	f		\N		
55	3	DNS Subdomain Bruteforce			f	t	subdomain_bruteforce.pl	f		\N		
39	3	DNS Top TLDs			f	t	dns_top_tlds.pl	f		\N		
53	14	SMTP Relay			f	t	smtp_relay.pl	f		\N		
52	15	Nmap Port Scan			f	t	pscan.pl	f		\N		
51	15	TCP Port Scan			f	t	portscan.pl	f		\N		
32	16	Apache DoS			f	t	apache_dos.pl	f		\N		
41	16	Fuzz Check			f	t	fuzz_check.pl	f		\N		
42	16	Google URL			f	t	google_url.pl	f		\N		
43	16	Grep URL			f	t	grep_url.pl	f	http	\N		
44	16	HTTP Banner			f	t	http_banner.pl	f	http	\N		
45	16	Joomla Scan			f	t	joomla_scan.pl	f	http	\N		
46	16	Login Pages			f	t	login_pages.pl	f	http	\N		
49	16	Nikto			f	t	nikto.pl	f	http	80		
56	16	URL Scan			f	t	urlscan.pl	f	http	\N		
61	16	Web Auth Scanner			f	t	www_auth_scanner.pl	f	http	80		
62	16	Web Directory Scanner			f	t	www_dir_scanner.pl	f	http	80		
63	16	Web File Scanner			f	t	www_file_scanner.pl	f	http	80		
57	16	Web Server CMS			f	t	webserver_cms.pl	f		\N		
58	16	Web Server Error Message			f	t	webserver_error_msg.pl	f		\N		
60	16	Web Server SSL			f	t	webserver_ssl.pl	f		\N		
59	16	Web Server Files			f	t	webserver_files.pl	f		\N		
\.


--
-- TOC entry 2027 (class 0 OID 16476)
-- Dependencies: 154
-- Data for Name: checks_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks_l10n (check_id, language_id, name, background_info, hints, reference, question) FROM stdin;
54	3	SSH Bruteforce			\N	\N
54	4				\N	\N
23	3	DNS Hosting			\N	\N
23	4				\N	\N
24	3	DNS SOA			\N	\N
24	4				\N	\N
25	3	DNS SPF			\N	\N
25	4				\N	\N
26	3	SMTP Banner			\N	\N
26	4				\N	\N
27	3	SMTP DNSBL			\N	\N
27	4				\N	\N
28	3	SMTP Filter			\N	\N
28	4				\N	\N
30	3	Web HTTP Methods			\N	\N
30	4				\N	\N
31	3	Web SQL XSS			\N	\N
31	4				\N	\N
29	3	TCP Traceroute			\N	\N
29	4				\N	\N
47	4	\N	\N	\N	\N	\N
48	3	DNS NIC Whois	\N	\N	\N	\N
22	3	DNS A (Non-Recursive)			\N	\N
22	4				\N	\N
48	4	\N	\N	\N	\N	\N
50	3	DNS NS Version	\N	\N	\N	\N
49	4	\N	\N	\N	\N	\N
56	3	URL Scan	\N	\N	\N	\N
56	4	\N	\N	\N	\N	\N
61	3	Web Auth Scanner	\N	\N	\N	\N
40	3	FTP Bruteforce			\N	\N
40	4				\N	\N
61	4	\N	\N	\N	\N	\N
62	3	Web Directory Scanner	\N	\N	\N	\N
62	4	\N	\N	\N	\N	\N
63	3	Web File Scanner	\N	\N	\N	\N
50	4	\N	\N	\N	\N	\N
63	4	\N	\N	\N	\N	\N
57	3	Web Server CMS	\N	\N	\N	\N
57	4	\N	\N	\N	\N	\N
58	3	Web Server Error Message	\N	\N	\N	\N
58	4	\N	\N	\N	\N	\N
60	3	Web Server SSL	\N	\N	\N	\N
60	4	\N	\N	\N	\N	\N
59	3	Web Server Files	\N	\N	\N	\N
37	3	DNS Resolve IP	\N	\N	\N	\N
37	4	\N	\N	\N	\N	\N
59	4	\N	\N	\N	\N	\N
33	3	DNS AFXR	\N	\N	\N	\N
38	3	DNS SPF (Perl)	\N	\N	\N	\N
38	4	\N	\N	\N	\N	\N
55	3	DNS Subdomain Bruteforce	\N	\N	\N	\N
55	4	\N	\N	\N	\N	\N
39	3	DNS Top TLDs	\N	\N	\N	\N
39	4	\N	\N	\N	\N	\N
53	3	SMTP Relay	\N	\N	\N	\N
53	4	\N	\N	\N	\N	\N
33	4	\N	\N	\N	\N	\N
34	3	DNS DOM MX	\N	\N	\N	\N
34	4	\N	\N	\N	\N	\N
35	3	DNS Find NS	\N	\N	\N	\N
35	4	\N	\N	\N	\N	\N
36	3	DNS IP Range	\N	\N	\N	\N
36	4	\N	\N	\N	\N	\N
47	3	DNS NIC Typosquatting	\N	\N	\N	\N
52	3	Nmap Port Scan	\N	\N	\N	\N
52	4	\N	\N	\N	\N	\N
51	3	TCP Port Scan	\N	\N	\N	\N
51	4	\N	\N	\N	\N	\N
32	3	Apache DoS	\N	\N	\N	\N
32	4	\N	\N	\N	\N	\N
41	3	Fuzz Check	\N	\N	\N	\N
41	4	\N	\N	\N	\N	\N
42	3	Google URL	\N	\N	\N	\N
42	4	\N	\N	\N	\N	\N
43	3	Grep URL	\N	\N	\N	\N
43	4	\N	\N	\N	\N	\N
44	3	HTTP Banner	\N	\N	\N	\N
44	4	\N	\N	\N	\N	\N
45	3	Joomla Scan	\N	\N	\N	\N
45	4	\N	\N	\N	\N	\N
46	3	Login Pages	\N	\N	\N	\N
46	4	\N	\N	\N	\N	\N
49	3	Nikto	\N	\N	\N	\N
1	3	DNS A	\N	\N	\N	\N
1	4	\N	\N	\N	\N	\N
\.


--
-- TOC entry 2028 (class 0 OID 16482)
-- Dependencies: 155
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY clients (id, name, country, state, city, address, postcode, website, contact_name, contact_phone, contact_email) FROM stdin;
15	Keke									
\.


--
-- TOC entry 2039 (class 0 OID 16903)
-- Dependencies: 173
-- Data for Name: emails; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY emails (id, user_id, subject, content, attempts, sent) FROM stdin;
\.


--
-- TOC entry 2029 (class 0 OID 16490)
-- Dependencies: 157
-- Data for Name: languages; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY languages (id, name, code, "default") FROM stdin;
3	English	en	t
4	Deutsch	de	f
\.


--
-- TOC entry 2030 (class 0 OID 16498)
-- Dependencies: 159
-- Data for Name: project_details; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_details (id, project_id, subject, content) FROM stdin;
\.


--
-- TOC entry 2031 (class 0 OID 16506)
-- Dependencies: 161
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY projects (id, client_id, year, deadline, name, status) FROM stdin;
13	15	2012	2012-07-08	Yay	in_progress
14	15	2013	2012-07-28	Yay	in_progress
\.


--
-- TOC entry 2032 (class 0 OID 16515)
-- Dependencies: 163
-- Data for Name: target_check_attachments; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_attachments (target_id, check_id, name, type, path, size) FROM stdin;
\.


--
-- TOC entry 2033 (class 0 OID 16522)
-- Dependencies: 164
-- Data for Name: target_check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_categories (target_id, check_category_id, advanced, check_count, finished_count, low_risk_count, med_risk_count, high_risk_count) FROM stdin;
20	15	t	3	0	0	0	0
21	14	t	4	0	0	0	0
21	3	t	16	2	0	0	0
18	3	t	16	2	1	0	1
19	3	t	16	0	0	0	0
22	3	t	16	6	0	0	0
20	3	t	16	1	0	0	0
20	16	t	18	0	0	0	0
19	14	t	4	3	1	1	0
\.


--
-- TOC entry 2034 (class 0 OID 16530)
-- Dependencies: 165
-- Data for Name: target_check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_inputs (target_id, check_input_id, value, file, check_id) FROM stdin;
18	22	0	\N	1
19	11	\N	\N	28
19	12	\N	\N	28
19	13	\N	\N	28
19	14	\N	\N	28
19	15	\N	\N	28
19	16	\N	\N	28
20	30	120	\N	35
20	31	0	\N	35
20	36	10	\N	47
20	37	100	\N	47
20	38	0	\N	47
21	22	0	\N	1
22	23	\N	\N	22
22	28	120	\N	34
22	29	0	\N	34
22	30	120	\N	35
22	31	0	\N	35
22	8	0	\N	23
22	22	0	\N	1
20	23	\N	\N	22
20	28	120	\N	34
20	29	0	\N	34
20	40	1-1024	b408abfe87974b91e305547214a2c4042cebfdf1069799023343312a428dcb77	52
20	41	1000	ef700b7fb73013345477e5e047b09c01f34f76395e8ef911bee7677e548f0032	52
20	8	0	4f571816ba14f5a3b446d2e842ac08ed02f4c8b0a91ce697563d0c0a463333b2	23
20	22	onexchanger.com	afd108acd167bb36a66e5ba202b9ae8b48e6ff5b55e3c8213e2c3291d2fe0fce	1
\.


--
-- TOC entry 2035 (class 0 OID 16536)
-- Dependencies: 166
-- Data for Name: target_check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_solutions (target_id, check_solution_id, check_id) FROM stdin;
\.


--
-- TOC entry 2036 (class 0 OID 16539)
-- Dependencies: 167
-- Data for Name: target_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_checks (target_id, check_id, result, target_file, rating, started, pid, status, result_file, language_id, protocol, port, override_target, user_id) FROM stdin;
20	23	0	69d256933858c9878c47160ee972a1dd8230f77e4e4fc45c1d4636f2c60ccd35	\N	2012-07-16 09:53:19.504801	\N	finished	d728e80a6c099cca9ca602724b94e972e68408bb89631262f9ff2cb9454bf915	3	\N	\N	netprotect.ch	1
20	1	DNS request timeout.\n	20a83892a155feebb9c1be2220a0bc2eed79ebca2bdc16fc3577c2ce0c6e7b27	\N	2012-07-16 09:58:02.904967	\N	finished	988926c2685920500f756c72206c9e4cac30adb5a8204f44f7c5d595fbb58c35	3	\N	\N	\N	1
20	52	No output.	a12f95c2f3ccfc6f4d30634f7eaa6eb4cb078c19b4143860f620a0467097e4c4	\N	2012-07-15 19:07:10.337175	\N	finished	dec30f2d197f002d44029a74e4ea29f2f20eb1c5f9afdf0ed101729ea9528190	3	\N	\N	\N	1
\.


--
-- TOC entry 2037 (class 0 OID 16546)
-- Dependencies: 168
-- Data for Name: targets; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY targets (id, project_id, host) FROM stdin;
18	13	onexchanger.com
19	13	ziliboba.com
21	14	test.com
22	13	test.com
20	14	onexchanger.com
\.


--
-- TOC entry 2038 (class 0 OID 16554)
-- Dependencies: 170
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY users (id, email, password, name, client_id, role) FROM stdin;
2	test@user.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		\N	user
16	test@client.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		\N	admin
1	erbol.turburgaev@gmail.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		\N	admin
\.


--
-- TOC entry 1938 (class 2606 OID 16575)
-- Dependencies: 142 142 142
-- Name: check_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_pkey PRIMARY KEY (check_category_id, language_id);


--
-- TOC entry 1936 (class 2606 OID 16577)
-- Dependencies: 140 140
-- Name: check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories
    ADD CONSTRAINT check_categories_pkey PRIMARY KEY (id);


--
-- TOC entry 1942 (class 2606 OID 16579)
-- Dependencies: 145 145 145
-- Name: check_inputs_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_pkey PRIMARY KEY (check_input_id, language_id);


--
-- TOC entry 1940 (class 2606 OID 16581)
-- Dependencies: 143 143
-- Name: check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_pkey PRIMARY KEY (id);


--
-- TOC entry 1946 (class 2606 OID 16583)
-- Dependencies: 148 148 148
-- Name: check_results_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_pkey PRIMARY KEY (check_result_id, language_id);


--
-- TOC entry 1944 (class 2606 OID 16585)
-- Dependencies: 146 146
-- Name: check_results_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_pkey PRIMARY KEY (id);


--
-- TOC entry 1950 (class 2606 OID 16587)
-- Dependencies: 151 151 151
-- Name: check_solutions_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_pkey PRIMARY KEY (check_solution_id, language_id);


--
-- TOC entry 1948 (class 2606 OID 16589)
-- Dependencies: 149 149
-- Name: check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_pkey PRIMARY KEY (id);


--
-- TOC entry 1954 (class 2606 OID 16591)
-- Dependencies: 154 154 154
-- Name: checks_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_pkey PRIMARY KEY (check_id, language_id);


--
-- TOC entry 1952 (class 2606 OID 16593)
-- Dependencies: 152 152
-- Name: checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_pkey PRIMARY KEY (id);


--
-- TOC entry 1956 (class 2606 OID 16595)
-- Dependencies: 155 155
-- Name: clients_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- TOC entry 1984 (class 2606 OID 16908)
-- Dependencies: 173 173
-- Name: emails_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_pkey PRIMARY KEY (id);


--
-- TOC entry 1958 (class 2606 OID 16597)
-- Dependencies: 157 157
-- Name: languages_code_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_code_key UNIQUE (code);


--
-- TOC entry 1960 (class 2606 OID 16599)
-- Dependencies: 157 157
-- Name: languages_name_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_name_key UNIQUE (name);


--
-- TOC entry 1962 (class 2606 OID 16601)
-- Dependencies: 157 157
-- Name: languages_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- TOC entry 1964 (class 2606 OID 16603)
-- Dependencies: 159 159
-- Name: project_details_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_pkey PRIMARY KEY (id);


--
-- TOC entry 1966 (class 2606 OID 16605)
-- Dependencies: 161 161
-- Name: projects_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- TOC entry 1968 (class 2606 OID 16607)
-- Dependencies: 163 163
-- Name: target_check_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_pkey PRIMARY KEY (path);


--
-- TOC entry 1970 (class 2606 OID 16609)
-- Dependencies: 164 164 164
-- Name: target_check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_pkey PRIMARY KEY (target_id, check_category_id);


--
-- TOC entry 1972 (class 2606 OID 16611)
-- Dependencies: 165 165 165
-- Name: target_check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_pkey PRIMARY KEY (target_id, check_input_id);


--
-- TOC entry 1974 (class 2606 OID 16613)
-- Dependencies: 166 166 166
-- Name: target_check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_pkey PRIMARY KEY (target_id, check_solution_id);


--
-- TOC entry 1976 (class 2606 OID 16615)
-- Dependencies: 167 167 167
-- Name: target_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_pkey PRIMARY KEY (target_id, check_id);


--
-- TOC entry 1978 (class 2606 OID 16617)
-- Dependencies: 168 168
-- Name: targets_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_pkey PRIMARY KEY (id);


--
-- TOC entry 1980 (class 2606 OID 16619)
-- Dependencies: 170 170
-- Name: users_email_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 1982 (class 2606 OID 16621)
-- Dependencies: 170 170
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 1985 (class 2606 OID 16622)
-- Dependencies: 142 1935 140
-- Name: check_categories_l10n_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1986 (class 2606 OID 16627)
-- Dependencies: 142 1961 157
-- Name: check_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1987 (class 2606 OID 16632)
-- Dependencies: 1951 152 143
-- Name: check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1988 (class 2606 OID 16637)
-- Dependencies: 143 1939 145
-- Name: check_inputs_l10n_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1989 (class 2606 OID 16642)
-- Dependencies: 145 157 1961
-- Name: check_inputs_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1990 (class 2606 OID 16647)
-- Dependencies: 146 1951 152
-- Name: check_results_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1991 (class 2606 OID 16652)
-- Dependencies: 146 148 1943
-- Name: check_results_l10n_check_result_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_check_result_id_fkey FOREIGN KEY (check_result_id) REFERENCES check_results(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1992 (class 2606 OID 16657)
-- Dependencies: 148 1961 157
-- Name: check_results_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1993 (class 2606 OID 16662)
-- Dependencies: 1951 149 152
-- Name: check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1994 (class 2606 OID 16667)
-- Dependencies: 149 151 1947
-- Name: check_solutions_l10n_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1995 (class 2606 OID 16672)
-- Dependencies: 157 151 1961
-- Name: check_solutions_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1996 (class 2606 OID 16851)
-- Dependencies: 1935 140 152
-- Name: checks_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1997 (class 2606 OID 16856)
-- Dependencies: 154 1951 152
-- Name: checks_l10n_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1998 (class 2606 OID 16861)
-- Dependencies: 1961 154 157
-- Name: checks_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2017 (class 2606 OID 16944)
-- Dependencies: 170 173 1981
-- Name: emails_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1999 (class 2606 OID 16692)
-- Dependencies: 159 1965 161
-- Name: project_details_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2000 (class 2606 OID 16697)
-- Dependencies: 161 1955 155
-- Name: projects_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2001 (class 2606 OID 16702)
-- Dependencies: 152 163 1951
-- Name: target_check_attachments_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2002 (class 2606 OID 16707)
-- Dependencies: 163 168 1977
-- Name: target_check_attachments_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2003 (class 2606 OID 16712)
-- Dependencies: 140 1935 164
-- Name: target_check_categories_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2004 (class 2606 OID 16717)
-- Dependencies: 1977 164 168
-- Name: target_check_categories_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2005 (class 2606 OID 16722)
-- Dependencies: 152 1951 165
-- Name: target_check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id);


--
-- TOC entry 2006 (class 2606 OID 16727)
-- Dependencies: 143 1939 165
-- Name: target_check_inputs_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2007 (class 2606 OID 16732)
-- Dependencies: 168 165 1977
-- Name: target_check_inputs_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2008 (class 2606 OID 16737)
-- Dependencies: 1951 166 152
-- Name: target_check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id);


--
-- TOC entry 2009 (class 2606 OID 16742)
-- Dependencies: 149 1947 166
-- Name: target_check_solutions_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2010 (class 2606 OID 16747)
-- Dependencies: 1977 166 168
-- Name: target_check_solutions_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2011 (class 2606 OID 16881)
-- Dependencies: 1951 167 152
-- Name: target_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2012 (class 2606 OID 16886)
-- Dependencies: 157 1961 167
-- Name: target_checks_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2013 (class 2606 OID 16891)
-- Dependencies: 167 168 1977
-- Name: target_checks_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2014 (class 2606 OID 16896)
-- Dependencies: 167 1981 170
-- Name: target_checks_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2015 (class 2606 OID 16767)
-- Dependencies: 168 1965 161
-- Name: targets_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2016 (class 2606 OID 16772)
-- Dependencies: 155 170 1955
-- Name: users_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2044 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2012-07-18 23:02:42 MSK

--
-- PostgreSQL database dump complete
--

