--
-- PostgreSQL database dump
--

-- Dumped from database version 8.4.12
-- Dumped by pg_dump version 9.1.3
-- Started on 2012-06-17 17:53:39 MSK

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- TOC entry 467 (class 1247 OID 16387)
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
-- TOC entry 470 (class 1247 OID 16394)
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
-- TOC entry 473 (class 1247 OID 16400)
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
-- TOC entry 476 (class 1247 OID 16405)
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
-- TOC entry 2030 (class 0 OID 0)
-- Dependencies: 141
-- Name: check_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_categories_id_seq OWNED BY check_categories.id;


--
-- TOC entry 2031 (class 0 OID 0)
-- Dependencies: 141
-- Name: check_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_categories_id_seq', 16, true);


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
-- Dependencies: 1903 6
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
-- TOC entry 2032 (class 0 OID 0)
-- Dependencies: 144
-- Name: check_inputs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_inputs_id_seq OWNED BY check_inputs.id;


--
-- TOC entry 2033 (class 0 OID 0)
-- Dependencies: 144
-- Name: check_inputs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_inputs_id_seq', 26, true);


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
-- Dependencies: 1905 6
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
-- TOC entry 2034 (class 0 OID 0)
-- Dependencies: 147
-- Name: check_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_results_id_seq OWNED BY check_results.id;


--
-- TOC entry 2035 (class 0 OID 0)
-- Dependencies: 147
-- Name: check_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_results_id_seq', 6, true);


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
-- Dependencies: 1907 6
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
-- TOC entry 2036 (class 0 OID 0)
-- Dependencies: 150
-- Name: check_solutions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_solutions_id_seq OWNED BY check_solutions.id;


--
-- TOC entry 2037 (class 0 OID 0)
-- Dependencies: 150
-- Name: check_solutions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_solutions_id_seq', 16, true);


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
    impact_info character varying,
    manual_info character varying,
    advanced boolean NOT NULL,
    automated boolean NOT NULL,
    script character varying(1000),
    multiple_solutions boolean NOT NULL,
    protocol character varying(1000),
    port integer
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
-- TOC entry 2038 (class 0 OID 0)
-- Dependencies: 153
-- Name: checks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE checks_id_seq OWNED BY checks.id;


--
-- TOC entry 2039 (class 0 OID 0)
-- Dependencies: 153
-- Name: checks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('checks_id_seq', 31, true);


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
    impact_info character varying,
    manual_info character varying
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
-- Dependencies: 155 6
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
-- TOC entry 2040 (class 0 OID 0)
-- Dependencies: 156
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE clients_id_seq OWNED BY clients.id;


--
-- TOC entry 2041 (class 0 OID 0)
-- Dependencies: 156
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('clients_id_seq', 14, true);


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
-- TOC entry 2042 (class 0 OID 0)
-- Dependencies: 158
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE languages_id_seq OWNED BY languages.id;


--
-- TOC entry 2043 (class 0 OID 0)
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
-- TOC entry 2044 (class 0 OID 0)
-- Dependencies: 160
-- Name: project_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE project_details_id_seq OWNED BY project_details.id;


--
-- TOC entry 2045 (class 0 OID 0)
-- Dependencies: 160
-- Name: project_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('project_details_id_seq', 5, true);


--
-- TOC entry 161 (class 1259 OID 16506)
-- Dependencies: 1913 473 6
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
-- Dependencies: 161 6
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
-- TOC entry 2046 (class 0 OID 0)
-- Dependencies: 162
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE projects_id_seq OWNED BY projects.id;


--
-- TOC entry 2047 (class 0 OID 0)
-- Dependencies: 162
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('projects_id_seq', 12, true);


--
-- TOC entry 163 (class 1259 OID 16515)
-- Dependencies: 1915 6
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
-- Dependencies: 1916 1917 1918 1919 1920 6
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
-- Dependencies: 1921 467 470 6
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
    override_target character varying(1000)
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
-- TOC entry 2048 (class 0 OID 0)
-- Dependencies: 169
-- Name: targets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE targets_id_seq OWNED BY targets.id;


--
-- TOC entry 2049 (class 0 OID 0)
-- Dependencies: 169
-- Name: targets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('targets_id_seq', 16, true);


--
-- TOC entry 170 (class 1259 OID 16554)
-- Dependencies: 1923 6 476
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
-- TOC entry 2050 (class 0 OID 0)
-- Dependencies: 171
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- TOC entry 2051 (class 0 OID 0)
-- Dependencies: 171
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('users_id_seq', 15, true);


--
-- TOC entry 1902 (class 2604 OID 16563)
-- Dependencies: 141 140
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories ALTER COLUMN id SET DEFAULT nextval('check_categories_id_seq'::regclass);


--
-- TOC entry 1904 (class 2604 OID 16564)
-- Dependencies: 144 143
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs ALTER COLUMN id SET DEFAULT nextval('check_inputs_id_seq'::regclass);


--
-- TOC entry 1906 (class 2604 OID 16565)
-- Dependencies: 147 146
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results ALTER COLUMN id SET DEFAULT nextval('check_results_id_seq'::regclass);


--
-- TOC entry 1908 (class 2604 OID 16566)
-- Dependencies: 150 149
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions ALTER COLUMN id SET DEFAULT nextval('check_solutions_id_seq'::regclass);


--
-- TOC entry 1909 (class 2604 OID 16567)
-- Dependencies: 153 152
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks ALTER COLUMN id SET DEFAULT nextval('checks_id_seq'::regclass);


--
-- TOC entry 1910 (class 2604 OID 16568)
-- Dependencies: 156 155
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY clients ALTER COLUMN id SET DEFAULT nextval('clients_id_seq'::regclass);


--
-- TOC entry 1911 (class 2604 OID 16569)
-- Dependencies: 158 157
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY languages ALTER COLUMN id SET DEFAULT nextval('languages_id_seq'::regclass);


--
-- TOC entry 1912 (class 2604 OID 16570)
-- Dependencies: 160 159
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details ALTER COLUMN id SET DEFAULT nextval('project_details_id_seq'::regclass);


--
-- TOC entry 1914 (class 2604 OID 16571)
-- Dependencies: 162 161
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects ALTER COLUMN id SET DEFAULT nextval('projects_id_seq'::regclass);


--
-- TOC entry 1922 (class 2604 OID 16572)
-- Dependencies: 169 168
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets ALTER COLUMN id SET DEFAULT nextval('targets_id_seq'::regclass);


--
-- TOC entry 1924 (class 2604 OID 16573)
-- Dependencies: 171 170
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- TOC entry 2004 (class 0 OID 16409)
-- Dependencies: 140
-- Data for Name: check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_categories (id, name) FROM stdin;
3	DNS
14	SMTP
15	TCP
16	Web
\.


--
-- TOC entry 2005 (class 0 OID 16417)
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
\.


--
-- TOC entry 2006 (class 0 OID 16423)
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
22	1	Hostname		0	
23	22	Hostname		0	
25	24	Hostname		0	
26	25	Hostname		0	
8	23	Show All		0	0
\.


--
-- TOC entry 2007 (class 0 OID 16432)
-- Dependencies: 145
-- Data for Name: check_inputs_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs_l10n (check_input_id, language_id, name, description, value) FROM stdin;
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
22	3	Hostname		
22	4			
23	3	Hostname		
23	4			
25	3	Hostname		
25	4			
26	3	Hostname		
26	4			
8	3	Show All		0
8	4			
\.


--
-- TOC entry 2008 (class 0 OID 16438)
-- Dependencies: 146
-- Data for Name: check_results; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results (id, check_id, result, sort_order) FROM stdin;
\.


--
-- TOC entry 2009 (class 0 OID 16447)
-- Dependencies: 148
-- Data for Name: check_results_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results_l10n (check_result_id, language_id, result) FROM stdin;
\.


--
-- TOC entry 2010 (class 0 OID 16453)
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
-- TOC entry 2011 (class 0 OID 16462)
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
-- TOC entry 2012 (class 0 OID 16468)
-- Dependencies: 152
-- Data for Name: checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks (id, check_category_id, name, background_info, impact_info, manual_info, advanced, automated, script, multiple_solutions, protocol, port) FROM stdin;
23	3	DNS Hosting				f	t	dns_hosting.py	f	\N	\N
24	3	DNS SOA				f	t	dns_soa.py	f	\N	\N
25	3	DNS SPF				f	t	dns_spf.py	f	\N	\N
26	14	SMTP Banner				f	t	smtp_banner.py	f	\N	\N
27	14	SMTP DNSBL				f	t	smtp_dnsbl.py	f	\N	\N
28	14	SMTP Filter				f	t	smtp_filter.py	f	\N	\N
30	16	Web HTTP Methods				f	t	web_http_methods.py	f	\N	\N
31	16	Web SQL XSS				f	t	web_sql_xss.py	f	\N	\N
29	15	TCP Traceroute				f	t	tcp_traceroute.py	f		80
1	3	DNS A	Checks DNS A records for the domain.	Not too much impact.		f	t	dns_a.py	f		\N
22	3	DNS A (Non-Recursive)				f	t	dns_a_nr.py	t		\N
\.


--
-- TOC entry 2013 (class 0 OID 16476)
-- Dependencies: 154
-- Data for Name: checks_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks_l10n (check_id, language_id, name, background_info, impact_info, manual_info) FROM stdin;
23	3	DNS Hosting			
23	4				
24	3	DNS SOA			
24	4				
25	3	DNS SPF			
25	4				
26	3	SMTP Banner			
26	4				
27	3	SMTP DNSBL			
27	4				
28	3	SMTP Filter			
28	4				
30	3	Web HTTP Methods			
30	4				
31	3	Web SQL XSS			
31	4				
29	3	TCP Traceroute			
29	4				
1	3	DNS A	Checks DNS A records for the domain.	Not too much impact.	
1	4	Deutsche DNS A			
22	3	DNS A (Non-Recursive)			
22	4				
\.


--
-- TOC entry 2014 (class 0 OID 16482)
-- Dependencies: 155
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY clients (id, name, country, state, city, address, postcode, website, contact_name, contact_phone, contact_email) FROM stdin;
9	Airbus	\N	\N	\N	\N	\N	\N	\N	\N	\N
1	Netprotect AG	Switzerland		Bern			http://www.netprotect.ch	Oliver Muenchow		oliver@muenchow.com
\.


--
-- TOC entry 2015 (class 0 OID 16490)
-- Dependencies: 157
-- Data for Name: languages; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY languages (id, name, code, "default") FROM stdin;
3	English	en	t
4	Deutsch	de	f
\.


--
-- TOC entry 2016 (class 0 OID 16498)
-- Dependencies: 159
-- Data for Name: project_details; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_details (id, project_id, subject, content) FROM stdin;
2	1	Test Server	http://test.local
5	1	Server Details	IP: 127.0.0.1
1	1	Login Details	ZOXOXOXO
\.


--
-- TOC entry 2017 (class 0 OID 16506)
-- Dependencies: 161
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY projects (id, client_id, year, deadline, name, status) FROM stdin;
1	1	2013	2012-07-14	Complex Test	in_progress
8	9	2012	2012-05-17	Airbus Test	finished
12	1	2012	2012-06-14	Yay	in_progress
\.


--
-- TOC entry 2018 (class 0 OID 16515)
-- Dependencies: 163
-- Data for Name: target_check_attachments; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_attachments (target_id, check_id, name, type, path, size) FROM stdin;
\.


--
-- TOC entry 2019 (class 0 OID 16522)
-- Dependencies: 164
-- Data for Name: target_check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_categories (target_id, check_category_id, advanced, check_count, finished_count, low_risk_count, med_risk_count, high_risk_count) FROM stdin;
3	3	t	5	5	2	1	1
14	14	t	3	3	0	0	0
14	15	t	1	1	0	0	0
16	3	t	5	1	1	0	0
15	16	t	2	2	0	1	0
12	3	t	5	0	0	0	0
\.


--
-- TOC entry 2020 (class 0 OID 16530)
-- Dependencies: 165
-- Data for Name: target_check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_inputs (target_id, check_input_id, value, file, check_id) FROM stdin;
15	19	php	\N	31
15	20		\N	31
15	21	100	\N	31
3	22	onexchanger.com	\N	1
3	8	no	\N	23
3	25	onexchanger.com	\N	24
14	11	erbol.turburgaev@gmail.com	\N	28
14	12	smtp.yandex.ru	\N	28
14	13	emailreminder@yandex.ru	\N	28
14	14	123321q	\N	28
14	15	emailreminder@yandex.ru	\N	28
14	16		\N	28
3	23	onexchanger.com	\N	22
3	26	onexchanger.com	\N	25
16	23		\N	22
\.


--
-- TOC entry 2021 (class 0 OID 16536)
-- Dependencies: 166
-- Data for Name: target_check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_solutions (target_id, check_solution_id, check_id) FROM stdin;
3	13	22
3	14	22
3	15	25
\.


--
-- TOC entry 2022 (class 0 OID 16539)
-- Dependencies: 167
-- Data for Name: target_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_checks (target_id, check_id, result, target_file, rating, started, pid, status, result_file, language_id, protocol, port, override_target) FROM stdin;
14	26	220 smtp13.mail.yandex.net (Want to use Yandex.Mail for your domain? Visit http://pdd.yandex.ru)	7d513c98074f61eef4d4c984393ffe31c9c9cbad689167ccca7b5f63afb48289	info	2012-06-12 05:25:05.712931	12730	finished	b3760668672a8b5f1b31cde0ca3fa91688e0752dd6dbf1bbf284fc20f45c96e0	3	\N	\N	smtp.yandex.ru
14	28	1.txt\nIOError: [Errno 2] No such file or directory: 'smtp_filter_files/Screenshot at 2012-04-11 16:24:25.png'	694d12d90e5d1ab60b1bd33ca3088b07271b1ae3c8667a0da85a4477830df8ca	info	2012-06-12 05:35:30.310166	12814	finished	29dc8d40545c6dc2c7bad2be1da7b9eeebd9fd296f02c93d82ffc726f1680d6c	3	\N	\N	
3	22	46.4.98.106	a3b5d1d991032fbb1fecc32b5ed3b0388e5e1a3232a62c66591f3eba6778500e	high_risk	2012-06-12 09:12:43.742355	14362	finished	1b615df970a64f80ef422c143656ab5ce2346c6fa0636b3aa092f3be828a8190	3		\N	
3	25	TXT "v=spf1 include:_spf.google.com ~all"\nSPF record is valid.\nIt's recommended to have SPF records of both SPF and TXT record types, RFC 4408.	8a22de3aa2781f8951be93a6de095b1646e138028b2bb7e542607c9bfdb73bd4	low_risk	2012-06-12 09:12:43.699735	14358	finished	bae324c9ce5b9777970f61cf65a36288fb07acd8a5dd211c68a35f1732101cfa	3		\N	
3	1	DNS request timeout.	f3e366d23cf1e347865d46a26708fa674591e7aeea247a546548b69a13e19420	low_risk	2012-06-12 09:12:53.309045	14360	finished	31cd0c2a1e51321841ab446044cb9feea4e3dbc5586d8dfab7a87459252b6e0e	3	\N	\N	google.com
3	23	No host names found.	7757e76b78d08db0d153ff74f40ae4c4044034b2e14a1930e2c6596f308058d4	med_risk	2012-06-12 09:12:50.928459	14364	finished	96e894e8dd2c99e3181b9dc7bab92cdabd2cac96579b566083a59ac34d601216	3	\N	\N	netprotect.ch
14	27	Server is not listed in any known blocklist.	afc4d8a54761bcda26dd083c40d50911b0912946412e56e8d60f4f25a69a231f	info	2012-06-12 05:32:50.887047	12788	finished	cc69e6ed1a72e861f726cb156a1fb89229cb954efba83b603ad7eafb65d6088b	3		\N	masadata.com
3	24	Nameserver                     IP                  SOA Serial      Refresh    Retry      Expire     Minimum\n-----------------------------------------------------------------------------------------------------------\ndns3.registrar-servers.com     IP 184.173.112.216  SOA 2011121800  2h 46m 41s 30m 1s     7d 1s      1h 1s\ndns4.registrar-servers.com     IP 37.58.77.234     SOA 2011121800  2h 46m 41s 30m 1s     7d 1s      1h 1s\ndns5.registrar-servers.com     IP 213.229.119.229  SOA 2011121800  2h 46m 41s 30m 1s     7d 1s      1h 1s\ndns1.registrar-servers.com     IP 69.16.244.25     SOA 2011121800  2h 46m 41s 30m 1s     7d 1s      1h 1s\ndns2.registrar-servers.com     IP 208.64.122.242   SOA 2011121800  2h 46m 41s 30m 1s     7d 1s      1h 1s\nThe recommended value for expire time is 2 to 4 weeks, RFC 1912 (7d 1s)\nThe recommended value for minimum TTL is 1 to 5 days, RFC 1912 (1h 1s)	f53bcefd4c634b5a52d2c25a4b7024e1905e8aceb848672981525971ad2ec33e	info	2012-06-12 09:12:44.409231	14366	finished	e1008408432c95a4f331bf4ebc3d018962ec29fd7649721bd799fced838d56ce	3	\N	\N	
15	30	Dangerous methods allowed: TRACE, PUT, DELETE.	e5a93ba7361c4a3ccba34a77730d001075875e2e98fb54e6cbf8acf578a08fc0	hidden	2012-06-12 09:19:15.11335	14511	finished	230cc8271566f26bb482b17d81e766f8b9c113a1e4c52c7f68fba52a0f12a08b	3	\N	\N	
15	31	No vulnerabilities detected.\nScanned 1 URLs.\n0.00 seconds elapsed.	753edbe0c96decc4d981e0cb1b8f77cd9d8b8779c16a08777794f88eb7114ab3	med_risk	2012-06-12 09:31:18.217071	14691	finished	3d551c5a78ed09106779c1cd1c2e5e15ece19fbc7c3374b0d9e77fe37f41f694	3	\N	\N	onexchanger.com
14	29	Selected device eth0, address 192.168.1.10, port 34950 for outgoing packets\nTracing the path to onexchanger.com (46.4.98.106) on TCP port 80 (www), 30 hops max\n 1  192.168.1.1  1.149 ms  1.156 ms  0.903 ms\n 2  host-205-43.telecet.ru (81.22.205.43)  17.331 ms  17.093 ms  18.933 ms\n 3  172.29.155.43  18.212 ms  17.249 ms  17.354 ms\n 4  host-205-9.telecet.ru (81.22.205.9)  17.975 ms  16.918 ms  17.675 ms\n 5  ae-2.750.kazn-rgr1.pv.ip.rostelecom.ru (94.25.11.165)  17.604 ms  18.051 ms  16.709 ms\n 6  xe-2-1-0.frkt-ar2.intl.ip.rostelecom.ru (87.226.139.25)  96.567 ms\n    188.254.44.182  88.998 ms\n    xe-10-2-0.frkt-ar2.intl.ip.rostelecom.ru (87.226.133.242)  76.469 ms\n 7  decix-gw.hetzner.de (80.81.192.164)  103.272 ms  102.820 ms  102.333 ms\n 8  hos-bb1.juniper1.rz14.hetzner.de (213.239.240.246)  86.585 ms\n    hos-bb1.juniper2.rz14.hetzner.de (213.239.240.247)  92.871 ms\n    hos-bb1.juniper1.rz14.hetzner.de (213.239.240.246)  86.939 ms\n 9  hos-tr2.ex3k8.rz14.hetzner.de (213.239.224.169)  95.831 ms  96.588 ms\n    hos-tr4.ex3k8.rz14.hetzner.de (213.239.224.233)  102.040 ms\n10  static.106.98.4.46.clients.your-server.de (46.4.98.106) [open]  90.696 ms  89.538 ms  90.538 ms\n	0b19a84b8dfd8a1ab42b70f244770363af7c8f65bd0c188628de1d3599ac4567	info	2012-06-12 09:13:55.256531	14451	finished	bb8f7ba0d9a1bd5f1bf032ad8984aa0516b089c4017c6aec75f0b4687382bff3	3		80	
16	22		\N	low_risk	\N	\N	finished	\N	3		\N	
\.


--
-- TOC entry 2023 (class 0 OID 16546)
-- Dependencies: 168
-- Data for Name: targets; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY targets (id, project_id, host) FROM stdin;
12	8	127.0.0.1
3	1	dns1.registrar-servers.com
14	1	onexchanger.com
15	1	gostrip.ru
16	12	dns1.registrar-servers.com
\.


--
-- TOC entry 2024 (class 0 OID 16554)
-- Dependencies: 170
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY users (id, email, password, name, client_id, role) FROM stdin;
1	test@admin.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		\N	admin
2	test@user.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3	Helloy	\N	user
3	test@client.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		1	client
\.


--
-- TOC entry 1928 (class 2606 OID 16575)
-- Dependencies: 142 142 142
-- Name: check_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_pkey PRIMARY KEY (check_category_id, language_id);


--
-- TOC entry 1926 (class 2606 OID 16577)
-- Dependencies: 140 140
-- Name: check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories
    ADD CONSTRAINT check_categories_pkey PRIMARY KEY (id);


--
-- TOC entry 1932 (class 2606 OID 16579)
-- Dependencies: 145 145 145
-- Name: check_inputs_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_pkey PRIMARY KEY (check_input_id, language_id);


--
-- TOC entry 1930 (class 2606 OID 16581)
-- Dependencies: 143 143
-- Name: check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_pkey PRIMARY KEY (id);


--
-- TOC entry 1936 (class 2606 OID 16583)
-- Dependencies: 148 148 148
-- Name: check_results_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_pkey PRIMARY KEY (check_result_id, language_id);


--
-- TOC entry 1934 (class 2606 OID 16585)
-- Dependencies: 146 146
-- Name: check_results_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_pkey PRIMARY KEY (id);


--
-- TOC entry 1940 (class 2606 OID 16587)
-- Dependencies: 151 151 151
-- Name: check_solutions_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_pkey PRIMARY KEY (check_solution_id, language_id);


--
-- TOC entry 1938 (class 2606 OID 16589)
-- Dependencies: 149 149
-- Name: check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_pkey PRIMARY KEY (id);


--
-- TOC entry 1944 (class 2606 OID 16591)
-- Dependencies: 154 154 154
-- Name: checks_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_pkey PRIMARY KEY (check_id, language_id);


--
-- TOC entry 1942 (class 2606 OID 16593)
-- Dependencies: 152 152
-- Name: checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_pkey PRIMARY KEY (id);


--
-- TOC entry 1946 (class 2606 OID 16595)
-- Dependencies: 155 155
-- Name: clients_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- TOC entry 1948 (class 2606 OID 16597)
-- Dependencies: 157 157
-- Name: languages_code_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_code_key UNIQUE (code);


--
-- TOC entry 1950 (class 2606 OID 16599)
-- Dependencies: 157 157
-- Name: languages_name_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_name_key UNIQUE (name);


--
-- TOC entry 1952 (class 2606 OID 16601)
-- Dependencies: 157 157
-- Name: languages_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- TOC entry 1954 (class 2606 OID 16603)
-- Dependencies: 159 159
-- Name: project_details_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_pkey PRIMARY KEY (id);


--
-- TOC entry 1956 (class 2606 OID 16605)
-- Dependencies: 161 161
-- Name: projects_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- TOC entry 1958 (class 2606 OID 16607)
-- Dependencies: 163 163
-- Name: target_check_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_pkey PRIMARY KEY (path);


--
-- TOC entry 1960 (class 2606 OID 16609)
-- Dependencies: 164 164 164
-- Name: target_check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_pkey PRIMARY KEY (target_id, check_category_id);


--
-- TOC entry 1962 (class 2606 OID 16611)
-- Dependencies: 165 165 165
-- Name: target_check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_pkey PRIMARY KEY (target_id, check_input_id);


--
-- TOC entry 1964 (class 2606 OID 16613)
-- Dependencies: 166 166 166
-- Name: target_check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_pkey PRIMARY KEY (target_id, check_solution_id);


--
-- TOC entry 1966 (class 2606 OID 16615)
-- Dependencies: 167 167 167
-- Name: target_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_pkey PRIMARY KEY (target_id, check_id);


--
-- TOC entry 1968 (class 2606 OID 16617)
-- Dependencies: 168 168
-- Name: targets_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_pkey PRIMARY KEY (id);


--
-- TOC entry 1970 (class 2606 OID 16619)
-- Dependencies: 170 170
-- Name: users_email_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 1972 (class 2606 OID 16621)
-- Dependencies: 170 170
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 1973 (class 2606 OID 16622)
-- Dependencies: 1925 142 140
-- Name: check_categories_l10n_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1974 (class 2606 OID 16627)
-- Dependencies: 157 142 1951
-- Name: check_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1975 (class 2606 OID 16632)
-- Dependencies: 1941 152 143
-- Name: check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1976 (class 2606 OID 16637)
-- Dependencies: 145 1929 143
-- Name: check_inputs_l10n_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1977 (class 2606 OID 16642)
-- Dependencies: 1951 157 145
-- Name: check_inputs_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1978 (class 2606 OID 16647)
-- Dependencies: 146 1941 152
-- Name: check_results_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1979 (class 2606 OID 16652)
-- Dependencies: 148 146 1933
-- Name: check_results_l10n_check_result_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_check_result_id_fkey FOREIGN KEY (check_result_id) REFERENCES check_results(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1980 (class 2606 OID 16657)
-- Dependencies: 1951 148 157
-- Name: check_results_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1981 (class 2606 OID 16662)
-- Dependencies: 1941 149 152
-- Name: check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1982 (class 2606 OID 16667)
-- Dependencies: 1937 149 151
-- Name: check_solutions_l10n_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1983 (class 2606 OID 16672)
-- Dependencies: 157 151 1951
-- Name: check_solutions_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1984 (class 2606 OID 16808)
-- Dependencies: 152 1925 140
-- Name: checks_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1985 (class 2606 OID 16682)
-- Dependencies: 154 152 1941
-- Name: checks_l10n_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1986 (class 2606 OID 16687)
-- Dependencies: 1951 154 157
-- Name: checks_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1987 (class 2606 OID 16692)
-- Dependencies: 159 161 1955
-- Name: project_details_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1988 (class 2606 OID 16697)
-- Dependencies: 161 155 1945
-- Name: projects_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1989 (class 2606 OID 16702)
-- Dependencies: 163 1941 152
-- Name: target_check_attachments_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1990 (class 2606 OID 16707)
-- Dependencies: 163 1967 168
-- Name: target_check_attachments_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1991 (class 2606 OID 16712)
-- Dependencies: 164 140 1925
-- Name: target_check_categories_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1992 (class 2606 OID 16717)
-- Dependencies: 1967 164 168
-- Name: target_check_categories_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1993 (class 2606 OID 16722)
-- Dependencies: 1941 165 152
-- Name: target_check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id);


--
-- TOC entry 1994 (class 2606 OID 16727)
-- Dependencies: 165 1929 143
-- Name: target_check_inputs_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1995 (class 2606 OID 16732)
-- Dependencies: 168 1967 165
-- Name: target_check_inputs_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1996 (class 2606 OID 16737)
-- Dependencies: 152 1941 166
-- Name: target_check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id);


--
-- TOC entry 1997 (class 2606 OID 16742)
-- Dependencies: 166 149 1937
-- Name: target_check_solutions_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1998 (class 2606 OID 16747)
-- Dependencies: 166 168 1967
-- Name: target_check_solutions_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1999 (class 2606 OID 16793)
-- Dependencies: 1941 152 167
-- Name: target_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2000 (class 2606 OID 16798)
-- Dependencies: 1951 157 167
-- Name: target_checks_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2001 (class 2606 OID 16803)
-- Dependencies: 168 1967 167
-- Name: target_checks_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2002 (class 2606 OID 16767)
-- Dependencies: 161 168 1955
-- Name: targets_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2003 (class 2606 OID 16772)
-- Dependencies: 1945 170 155
-- Name: users_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2029 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2012-06-17 17:53:40 MSK

--
-- PostgreSQL database dump complete
--

