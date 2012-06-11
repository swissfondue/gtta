--
-- PostgreSQL database dump
--

-- Dumped from database version 8.4.11
-- Dumped by pg_dump version 9.1.3
-- Started on 2012-06-10 18:53:44 MSK

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- TOC entry 563 (class 1247 OID 16808)
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
-- TOC entry 568 (class 1247 OID 16939)
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
-- TOC entry 467 (class 1247 OID 16392)
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
-- TOC entry 559 (class 1247 OID 16803)
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
-- TOC entry 140 (class 1259 OID 16401)
-- Dependencies: 6
-- Name: check_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_categories (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL
);


ALTER TABLE public.check_categories OWNER TO gtta;

--
-- TOC entry 141 (class 1259 OID 16407)
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

SELECT pg_catalog.setval('check_categories_id_seq', 13, true);


--
-- TOC entry 142 (class 1259 OID 16409)
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
-- TOC entry 143 (class 1259 OID 16415)
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
-- TOC entry 144 (class 1259 OID 16422)
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

SELECT pg_catalog.setval('check_inputs_id_seq', 5, true);


--
-- TOC entry 145 (class 1259 OID 16424)
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
-- TOC entry 146 (class 1259 OID 16430)
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
-- TOC entry 147 (class 1259 OID 16437)
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
-- TOC entry 148 (class 1259 OID 16439)
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
-- TOC entry 149 (class 1259 OID 16445)
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
-- TOC entry 150 (class 1259 OID 16452)
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

SELECT pg_catalog.setval('check_solutions_id_seq', 12, true);


--
-- TOC entry 151 (class 1259 OID 16454)
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
-- TOC entry 152 (class 1259 OID 16460)
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
    multiple_solutions boolean NOT NULL
);


ALTER TABLE public.checks OWNER TO gtta;

--
-- TOC entry 153 (class 1259 OID 16466)
-- Dependencies: 6 152
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

SELECT pg_catalog.setval('checks_id_seq', 21, true);


--
-- TOC entry 154 (class 1259 OID 16468)
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
-- TOC entry 155 (class 1259 OID 16474)
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
-- TOC entry 156 (class 1259 OID 16480)
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
-- TOC entry 157 (class 1259 OID 16482)
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
-- TOC entry 158 (class 1259 OID 16488)
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
-- TOC entry 159 (class 1259 OID 16490)
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
-- TOC entry 160 (class 1259 OID 16496)
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
-- TOC entry 161 (class 1259 OID 16498)
-- Dependencies: 1914 467 6
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
-- TOC entry 162 (class 1259 OID 16504)
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

SELECT pg_catalog.setval('projects_id_seq', 11, true);


--
-- TOC entry 163 (class 1259 OID 16506)
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
-- TOC entry 164 (class 1259 OID 16512)
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
-- TOC entry 165 (class 1259 OID 16520)
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
-- TOC entry 166 (class 1259 OID 16530)
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
-- TOC entry 167 (class 1259 OID 16533)
-- Dependencies: 1921 6 563 568
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
    port integer
);


ALTER TABLE public.target_checks OWNER TO gtta;

--
-- TOC entry 168 (class 1259 OID 16540)
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
-- TOC entry 169 (class 1259 OID 16546)
-- Dependencies: 168 6
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

SELECT pg_catalog.setval('targets_id_seq', 13, true);


--
-- TOC entry 170 (class 1259 OID 16548)
-- Dependencies: 1924 559 6
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
-- TOC entry 171 (class 1259 OID 16554)
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
-- TOC entry 1902 (class 2604 OID 16556)
-- Dependencies: 141 140
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories ALTER COLUMN id SET DEFAULT nextval('check_categories_id_seq'::regclass);


--
-- TOC entry 1904 (class 2604 OID 16557)
-- Dependencies: 144 143
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs ALTER COLUMN id SET DEFAULT nextval('check_inputs_id_seq'::regclass);


--
-- TOC entry 1906 (class 2604 OID 16558)
-- Dependencies: 147 146
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results ALTER COLUMN id SET DEFAULT nextval('check_results_id_seq'::regclass);


--
-- TOC entry 1908 (class 2604 OID 16559)
-- Dependencies: 150 149
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions ALTER COLUMN id SET DEFAULT nextval('check_solutions_id_seq'::regclass);


--
-- TOC entry 1909 (class 2604 OID 16560)
-- Dependencies: 153 152
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks ALTER COLUMN id SET DEFAULT nextval('checks_id_seq'::regclass);


--
-- TOC entry 1910 (class 2604 OID 16561)
-- Dependencies: 156 155
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY clients ALTER COLUMN id SET DEFAULT nextval('clients_id_seq'::regclass);


--
-- TOC entry 1911 (class 2604 OID 16562)
-- Dependencies: 158 157
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY languages ALTER COLUMN id SET DEFAULT nextval('languages_id_seq'::regclass);


--
-- TOC entry 1912 (class 2604 OID 16563)
-- Dependencies: 160 159
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details ALTER COLUMN id SET DEFAULT nextval('project_details_id_seq'::regclass);


--
-- TOC entry 1913 (class 2604 OID 16564)
-- Dependencies: 162 161
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects ALTER COLUMN id SET DEFAULT nextval('projects_id_seq'::regclass);


--
-- TOC entry 1922 (class 2604 OID 16565)
-- Dependencies: 169 168
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets ALTER COLUMN id SET DEFAULT nextval('targets_id_seq'::regclass);


--
-- TOC entry 1923 (class 2604 OID 16566)
-- Dependencies: 171 170
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- TOC entry 2004 (class 0 OID 16401)
-- Dependencies: 140
-- Data for Name: check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_categories (id, name) FROM stdin;
3	DNS
\.


--
-- TOC entry 2005 (class 0 OID 16409)
-- Dependencies: 142
-- Data for Name: check_categories_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_categories_l10n (check_category_id, language_id, name) FROM stdin;
3	3	DNS
3	4	DNS
\.


--
-- TOC entry 2006 (class 0 OID 16415)
-- Dependencies: 143
-- Data for Name: check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs (id, check_id, name, description, sort_order, value) FROM stdin;
5	1	Hostname	Please enter your hostname here.	0	Oyaebu ;)
\.


--
-- TOC entry 2007 (class 0 OID 16424)
-- Dependencies: 145
-- Data for Name: check_inputs_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs_l10n (check_input_id, language_id, name, description, value) FROM stdin;
5	3	Hostname	Please enter your hostname here.	Oyaebu ;)
5	4			
\.


--
-- TOC entry 2008 (class 0 OID 16430)
-- Dependencies: 146
-- Data for Name: check_results; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results (id, check_id, result, sort_order) FROM stdin;
5	1	This is a test.	0
\.


--
-- TOC entry 2009 (class 0 OID 16439)
-- Dependencies: 148
-- Data for Name: check_results_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results_l10n (check_result_id, language_id, result) FROM stdin;
5	3	This is a test.
5	4	
\.


--
-- TOC entry 2010 (class 0 OID 16445)
-- Dependencies: 149
-- Data for Name: check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions (id, check_id, solution, sort_order) FROM stdin;
11	1	guest mix	0
1	1	Test Solution	9
6	1	facebooook	4
7	1	quick break	5
10	1	lalalal	7
2	1	heeey	9
3	1	wooow	10
\.


--
-- TOC entry 2011 (class 0 OID 16454)
-- Dependencies: 151
-- Data for Name: check_solutions_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions_l10n (check_solution_id, language_id, solution) FROM stdin;
11	3	guest mix
11	4	
1	3	Test Solution
1	4	Testen Saluten
6	3	facebooook
6	4	
7	3	quick break
7	4	
10	3	lalalal
10	4	
2	3	heeey
2	4	
3	3	wooow
3	4	
\.


--
-- TOC entry 2012 (class 0 OID 16460)
-- Dependencies: 152
-- Data for Name: checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks (id, check_category_id, name, background_info, impact_info, manual_info, advanced, automated, script, multiple_solutions) FROM stdin;
3	3	DNS SPF	xxx	yyy	zzz	t	t		t
17	3	DNS MX				t	t		f
4	3	DNS NS Lookup				t	f		f
6	3	DNS LOL				t	f		f
5	3	DNS SND				t	f		f
8	3	DNS Check				f	f		f
1	3	DNS A	DNS A query check.	No impact.	dig <HOST> A	t	t	dns_a.py	f
\.


--
-- TOC entry 2013 (class 0 OID 16468)
-- Dependencies: 154
-- Data for Name: checks_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks_l10n (check_id, language_id, name, background_info, impact_info, manual_info) FROM stdin;
5	3	DNS SND			
5	4				
8	3	DNS Check			
8	4				
3	3	DNS SPF	xxx	yyy	zzz
3	4	DNS SPF DE	Hello World		
17	3	DNS MX			
17	4				
4	3	DNS NS Lookup			
4	4				
6	3	DNS LOL			
6	4				
1	3	DNS A	DNS A query check.	No impact.	dig <HOST> A
1	4	DNS A (DEUTSCH)	DNS A query check (DEUTSCH).	(DEUTSCH)	(DEUTSCH)
\.


--
-- TOC entry 2014 (class 0 OID 16474)
-- Dependencies: 155
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY clients (id, name, country, state, city, address, postcode, website, contact_name, contact_phone, contact_email) FROM stdin;
9	Airbus	\N	\N	\N	\N	\N	\N	\N	\N	\N
1	Netprotect AG	Switzerland		Bern			http://www.netprotect.ch	Oliver Muenchow		oliver@muenchow.com
\.


--
-- TOC entry 2015 (class 0 OID 16482)
-- Dependencies: 157
-- Data for Name: languages; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY languages (id, name, code, "default") FROM stdin;
3	English	en	t
4	Deutsch	de	f
\.


--
-- TOC entry 2016 (class 0 OID 16490)
-- Dependencies: 159
-- Data for Name: project_details; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_details (id, project_id, subject, content) FROM stdin;
2	1	Test Server	http://test.local
5	1	Server Details	IP: 127.0.0.1
1	1	Login Details	ZOXOXOXO
\.


--
-- TOC entry 2017 (class 0 OID 16498)
-- Dependencies: 161
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY projects (id, client_id, year, deadline, name, status) FROM stdin;
1	1	2013	2012-07-14	Complex Test	in_progress
8	9	2012	2012-05-17	Airbus Test	finished
\.


--
-- TOC entry 2018 (class 0 OID 16506)
-- Dependencies: 163
-- Data for Name: target_check_attachments; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_attachments (target_id, check_id, name, type, path, size) FROM stdin;
\.


--
-- TOC entry 2019 (class 0 OID 16512)
-- Dependencies: 164
-- Data for Name: target_check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_categories (target_id, check_category_id, advanced, check_count, finished_count, low_risk_count, med_risk_count, high_risk_count) FROM stdin;
12	3	t	7	0	0	0	0
3	3	t	7	0	0	0	0
\.


--
-- TOC entry 2020 (class 0 OID 16520)
-- Dependencies: 165
-- Data for Name: target_check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_inputs (target_id, check_input_id, value, file, check_id) FROM stdin;
3	5	onexchanger.com	abee414315ca5c827b4143ec6dd3e396aee42765c1f61f508b76be102b2f8122	1
\.


--
-- TOC entry 2021 (class 0 OID 16530)
-- Dependencies: 166
-- Data for Name: target_check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_solutions (target_id, check_solution_id, check_id) FROM stdin;
\.


--
-- TOC entry 2022 (class 0 OID 16533)
-- Dependencies: 167
-- Data for Name: target_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_checks (target_id, check_id, result, target_file, rating, started, pid, status, result_file, language_id, protocol, port) FROM stdin;
3	1	Host not found.	9acb5f18133e0f9379171accf5b893ca3b793ac89388a841d7848954c199688f	\N	2012-06-10 17:14:17.933567	1885	finished	ee6d8947d2128392b879c3134b5e7f2541a65d4d879eebb405e71b9f4067fa15	3	http	80
\.


--
-- TOC entry 2023 (class 0 OID 16540)
-- Dependencies: 168
-- Data for Name: targets; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY targets (id, project_id, host) FROM stdin;
3	1	netprotect.ch
12	8	127.0.0.1
\.


--
-- TOC entry 2024 (class 0 OID 16548)
-- Dependencies: 170
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY users (id, email, password, name, client_id, role) FROM stdin;
1	test@admin.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		\N	admin
2	test@user.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3	Helloy	\N	user
3	test@client.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		1	client
\.


--
-- TOC entry 1928 (class 2606 OID 16568)
-- Dependencies: 142 142 142
-- Name: check_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_pkey PRIMARY KEY (check_category_id, language_id);


--
-- TOC entry 1926 (class 2606 OID 16570)
-- Dependencies: 140 140
-- Name: check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories
    ADD CONSTRAINT check_categories_pkey PRIMARY KEY (id);


--
-- TOC entry 1932 (class 2606 OID 16572)
-- Dependencies: 145 145 145
-- Name: check_inputs_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_pkey PRIMARY KEY (check_input_id, language_id);


--
-- TOC entry 1930 (class 2606 OID 16574)
-- Dependencies: 143 143
-- Name: check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_pkey PRIMARY KEY (id);


--
-- TOC entry 1936 (class 2606 OID 16576)
-- Dependencies: 148 148 148
-- Name: check_results_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_pkey PRIMARY KEY (check_result_id, language_id);


--
-- TOC entry 1934 (class 2606 OID 16578)
-- Dependencies: 146 146
-- Name: check_results_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_pkey PRIMARY KEY (id);


--
-- TOC entry 1940 (class 2606 OID 16580)
-- Dependencies: 151 151 151
-- Name: check_solutions_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_pkey PRIMARY KEY (check_solution_id, language_id);


--
-- TOC entry 1938 (class 2606 OID 16582)
-- Dependencies: 149 149
-- Name: check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_pkey PRIMARY KEY (id);


--
-- TOC entry 1944 (class 2606 OID 16584)
-- Dependencies: 154 154 154
-- Name: checks_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_pkey PRIMARY KEY (check_id, language_id);


--
-- TOC entry 1942 (class 2606 OID 16586)
-- Dependencies: 152 152
-- Name: checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_pkey PRIMARY KEY (id);


--
-- TOC entry 1946 (class 2606 OID 16588)
-- Dependencies: 155 155
-- Name: clients_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- TOC entry 1948 (class 2606 OID 16590)
-- Dependencies: 157 157
-- Name: languages_code_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_code_key UNIQUE (code);


--
-- TOC entry 1950 (class 2606 OID 16592)
-- Dependencies: 157 157
-- Name: languages_name_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_name_key UNIQUE (name);


--
-- TOC entry 1952 (class 2606 OID 16594)
-- Dependencies: 157 157
-- Name: languages_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- TOC entry 1954 (class 2606 OID 16596)
-- Dependencies: 159 159
-- Name: project_details_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_pkey PRIMARY KEY (id);


--
-- TOC entry 1956 (class 2606 OID 16598)
-- Dependencies: 161 161
-- Name: projects_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- TOC entry 1958 (class 2606 OID 16907)
-- Dependencies: 163 163
-- Name: target_check_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_pkey PRIMARY KEY (path);


--
-- TOC entry 1960 (class 2606 OID 16602)
-- Dependencies: 164 164 164
-- Name: target_check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_pkey PRIMARY KEY (target_id, check_category_id);


--
-- TOC entry 1962 (class 2606 OID 16604)
-- Dependencies: 165 165 165
-- Name: target_check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_pkey PRIMARY KEY (target_id, check_input_id);


--
-- TOC entry 1964 (class 2606 OID 16608)
-- Dependencies: 166 166 166
-- Name: target_check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_pkey PRIMARY KEY (target_id, check_solution_id);


--
-- TOC entry 1966 (class 2606 OID 16610)
-- Dependencies: 167 167 167
-- Name: target_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_pkey PRIMARY KEY (target_id, check_id);


--
-- TOC entry 1968 (class 2606 OID 16612)
-- Dependencies: 168 168
-- Name: targets_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_pkey PRIMARY KEY (id);


--
-- TOC entry 1970 (class 2606 OID 16614)
-- Dependencies: 170 170
-- Name: users_email_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 1972 (class 2606 OID 16616)
-- Dependencies: 170 170
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 1973 (class 2606 OID 16617)
-- Dependencies: 140 142 1925
-- Name: check_categories_l10n_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1974 (class 2606 OID 16622)
-- Dependencies: 1951 157 142
-- Name: check_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1975 (class 2606 OID 16627)
-- Dependencies: 1941 152 143
-- Name: check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1976 (class 2606 OID 16632)
-- Dependencies: 145 1929 143
-- Name: check_inputs_l10n_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1977 (class 2606 OID 16637)
-- Dependencies: 145 157 1951
-- Name: check_inputs_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1978 (class 2606 OID 16642)
-- Dependencies: 1941 146 152
-- Name: check_results_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1979 (class 2606 OID 16647)
-- Dependencies: 1933 146 148
-- Name: check_results_l10n_check_result_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_check_result_id_fkey FOREIGN KEY (check_result_id) REFERENCES check_results(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1980 (class 2606 OID 16652)
-- Dependencies: 148 157 1951
-- Name: check_results_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1981 (class 2606 OID 16657)
-- Dependencies: 1941 149 152
-- Name: check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1982 (class 2606 OID 16662)
-- Dependencies: 149 1937 151
-- Name: check_solutions_l10n_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1983 (class 2606 OID 16667)
-- Dependencies: 151 157 1951
-- Name: check_solutions_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1984 (class 2606 OID 16672)
-- Dependencies: 152 140 1925
-- Name: checks_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1985 (class 2606 OID 16677)
-- Dependencies: 154 152 1941
-- Name: checks_l10n_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1986 (class 2606 OID 16682)
-- Dependencies: 154 157 1951
-- Name: checks_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1987 (class 2606 OID 16687)
-- Dependencies: 159 161 1955
-- Name: project_details_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1988 (class 2606 OID 16873)
-- Dependencies: 1945 161 155
-- Name: projects_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1989 (class 2606 OID 16896)
-- Dependencies: 1941 163 152
-- Name: target_check_attachments_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1990 (class 2606 OID 16901)
-- Dependencies: 168 1967 163
-- Name: target_check_attachments_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1991 (class 2606 OID 16707)
-- Dependencies: 164 140 1925
-- Name: target_check_categories_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1992 (class 2606 OID 16712)
-- Dependencies: 164 168 1967
-- Name: target_check_categories_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1993 (class 2606 OID 17002)
-- Dependencies: 165 1941 152
-- Name: target_check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id);


--
-- TOC entry 1994 (class 2606 OID 17007)
-- Dependencies: 143 1929 165
-- Name: target_check_inputs_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1995 (class 2606 OID 17012)
-- Dependencies: 165 168 1967
-- Name: target_check_inputs_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1996 (class 2606 OID 16742)
-- Dependencies: 166 152 1941
-- Name: target_check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id);


--
-- TOC entry 1997 (class 2606 OID 16747)
-- Dependencies: 166 149 1937
-- Name: target_check_solutions_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1998 (class 2606 OID 16752)
-- Dependencies: 1967 166 168
-- Name: target_check_solutions_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1999 (class 2606 OID 17027)
-- Dependencies: 1941 167 152
-- Name: target_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2001 (class 2606 OID 17037)
-- Dependencies: 1951 167 157
-- Name: target_checks_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2000 (class 2606 OID 17032)
-- Dependencies: 168 167 1967
-- Name: target_checks_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2002 (class 2606 OID 16767)
-- Dependencies: 1955 168 161
-- Name: targets_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2003 (class 2606 OID 16856)
-- Dependencies: 1945 155 170
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


-- Completed on 2012-06-10 18:53:44 MSK

--
-- PostgreSQL database dump complete
--

