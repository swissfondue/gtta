--
-- PostgreSQL database dump
--

-- Dumped from database version 8.4.12
-- Dumped by pg_dump version 9.1.3
-- Started on 2012-07-07 15:24:20 MSK

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
    impact_info character varying,
    manual_info character varying,
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
    impact_info character varying,
    manual_info character varying,
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

SELECT pg_catalog.setval('targets_id_seq', 17, true);


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
17	FTP
18	SSH
19	Non automated
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
17	3	FTP
17	4	
18	3	SSH
18	4	
19	3	Non automated
19	4	\N
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
22	1	Hostname		0	0
\.


--
-- TOC entry 2007 (class 0 OID 16432)
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
22	3	Hostname		0
22	4			
\.


--
-- TOC entry 2008 (class 0 OID 16438)
-- Dependencies: 146
-- Data for Name: check_results; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results (id, check_id, result, sort_order) FROM stdin;
9	1	Hello, world!	0
10	1	Yay	1
\.


--
-- TOC entry 2009 (class 0 OID 16447)
-- Dependencies: 148
-- Data for Name: check_results_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results_l10n (check_result_id, language_id, result) FROM stdin;
9	3	Hello, world!
9	4	\N
10	3	Yay
10	4	\N
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

COPY checks (id, check_category_id, name, background_info, impact_info, manual_info, advanced, automated, script, multiple_solutions, protocol, port, reference, question) FROM stdin;
1	3	DNS A	Checks DNS A records for the domain.	Not too much impact.		f	t	dns_a.py	f		\N	Test Reference	Question Test
23	3	DNS Hosting				f	t	dns_hosting.py	f	\N	\N	\N	\N
24	3	DNS SOA				f	t	dns_soa.py	f	\N	\N	\N	\N
25	3	DNS SPF				f	t	dns_spf.py	f	\N	\N	\N	\N
26	14	SMTP Banner				f	t	smtp_banner.py	f	\N	\N	\N	\N
27	14	SMTP DNSBL				f	t	smtp_dnsbl.py	f	\N	\N	\N	\N
28	14	SMTP Filter				f	t	smtp_filter.py	f	\N	\N	\N	\N
30	16	Web HTTP Methods				f	t	web_http_methods.py	f	\N	\N	\N	\N
31	16	Web SQL XSS				f	t	web_sql_xss.py	f	\N	\N	\N	\N
29	15	TCP Traceroute				f	t	tcp_traceroute.py	f		80	\N	\N
22	3	DNS A (Non-Recursive)				f	t	dns_a_nr.py	t		\N	\N	\N
32	16	*Apache DoS				f	t	apache_dos.pl	f		\N	\N	\N
34	3	*DNS DOM MX				f	t	dns_dom_mx.pl	f		\N	\N	\N
35	3	*DNS Find NS				f	t	dns_find_ns.pl	f		\N	\N	\N
36	3	*DNS IP Range				f	t	dns_ip_range.pl	f		\N	\N	\N
37	3	*DNS Resolve IP				f	t	dns_resolve_ip.pl	f		\N	\N	\N
38	3	*DNS SPF				f	t	dns_spf.pl	f		\N	\N	\N
39	3	*DNS Top TLDs				f	t	dns_top_tlds.pl	f		\N	\N	\N
40	17	FTP Bruteforce				f	t	ftp_bruteforce.pl	f		\N	\N	\N
41	16	*Fuzz Check				f	t	fuzz_check.pl	f		\N	\N	\N
42	16	*Google URL				f	t	google_url.pl	f		\N	\N	\N
43	16	*Grep URL				f	t	grep_url.pl	f	http	\N	\N	\N
44	16	*HTTP Banner				f	t	http_banner.pl	f	http	\N	\N	\N
45	16	*Joomla Scan				f	t	joomla_scan.pl	f	http	\N	\N	\N
46	16	*Login Pages				f	t	login_pages.pl	f	http	\N	\N	\N
47	3	*DNS NIC Typosquatting				f	t	nic_typosquatting.pl	f		\N	\N	\N
48	3	*DNS NIC Whois				f	t	nic_whois.pl	f		\N	\N	\N
49	16	*Nikto				f	t	nikto.pl	f	http	80	\N	\N
50	3	*DNS NS Version				f	t	ns_version.pl	f		\N	\N	\N
51	15	*TCP Port Scan				f	t	portscan.pl	f		\N	\N	\N
52	15	*Nmap Port Scan				f	t	pscan.pl	f		\N	\N	\N
53	14	*SMTP Relay				f	t	smtp_relay.pl	f		\N	\N	\N
54	18	SSH Bruteforce				f	t	ssh_bruteforce.pl	f		\N	\N	\N
55	3	*DNS Subdomain Bruteforce				f	t	subdomain_bruteforce.pl	f		\N	\N	\N
56	16	*URL Scan				f	t	urlscan.pl	f	http	\N	\N	\N
57	16	*Web Server CMS				f	t	webserver_cms.pl	f		\N	\N	\N
58	16	*Web Server Error Message				f	t	webserver_error_msg.pl	f		\N	\N	\N
59	16	*Web Server Files				f	t	webserver_files.pl	f		\N	\N	\N
60	16	*Web Server SSL				f	t	webserver_ssl.pl	f		\N	\N	\N
61	16	*Web Auth Scanner				f	t	www_auth_scanner.pl	f	http	80	\N	\N
62	16	*Web Directory Scanner				f	t	www_dir_scanner.pl	f	http	80	\N	\N
63	16	*Web File Scanner				f	t	www_file_scanner.pl	f	http	80	\N	\N
33	3	*DNS AFXR				f	t	dns_afxr.pl	f		\N	\N	\N
64	19	Non Automated				f	f		f		\N	\N	\N
\.


--
-- TOC entry 2013 (class 0 OID 16476)
-- Dependencies: 154
-- Data for Name: checks_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks_l10n (check_id, language_id, name, background_info, impact_info, manual_info, reference, question) FROM stdin;
53	4					\N	\N
54	3	SSH Bruteforce				\N	\N
54	4					\N	\N
55	3	*DNS Subdomain Bruteforce				\N	\N
55	4					\N	\N
56	3	*URL Scan				\N	\N
23	3	DNS Hosting				\N	\N
23	4					\N	\N
24	3	DNS SOA				\N	\N
24	4					\N	\N
25	3	DNS SPF				\N	\N
25	4					\N	\N
26	3	SMTP Banner				\N	\N
26	4					\N	\N
27	3	SMTP DNSBL				\N	\N
27	4					\N	\N
28	3	SMTP Filter				\N	\N
28	4					\N	\N
30	3	Web HTTP Methods				\N	\N
30	4					\N	\N
31	3	Web SQL XSS				\N	\N
31	4					\N	\N
56	4					\N	\N
57	3	*Web Server CMS				\N	\N
57	4					\N	\N
29	3	TCP Traceroute				\N	\N
29	4					\N	\N
58	3	*Web Server Error Message				\N	\N
22	3	DNS A (Non-Recursive)				\N	\N
22	4					\N	\N
32	3	*Apache DoS				\N	\N
32	4					\N	\N
34	3	*DNS DOM MX				\N	\N
34	4					\N	\N
35	3	*DNS Find NS				\N	\N
35	4					\N	\N
36	3	*DNS IP Range				\N	\N
36	4					\N	\N
37	3	*DNS Resolve IP				\N	\N
37	4					\N	\N
38	3	*DNS SPF				\N	\N
38	4					\N	\N
39	3	*DNS Top TLDs				\N	\N
39	4					\N	\N
40	3	FTP Bruteforce				\N	\N
40	4					\N	\N
41	3	*Fuzz Check				\N	\N
41	4					\N	\N
42	3	*Google URL				\N	\N
42	4					\N	\N
43	3	*Grep URL				\N	\N
43	4					\N	\N
44	3	*HTTP Banner				\N	\N
44	4					\N	\N
45	3	*Joomla Scan				\N	\N
45	4					\N	\N
46	3	*Login Pages				\N	\N
46	4					\N	\N
47	3	*DNS NIC Typosquatting				\N	\N
47	4					\N	\N
48	3	*DNS NIC Whois				\N	\N
48	4					\N	\N
49	3	*Nikto				\N	\N
49	4					\N	\N
50	3	*DNS NS Version				\N	\N
50	4					\N	\N
51	3	*TCP Port Scan				\N	\N
51	4					\N	\N
52	3	*Nmap Port Scan				\N	\N
52	4					\N	\N
53	3	*SMTP Relay				\N	\N
58	4					\N	\N
59	3	*Web Server Files				\N	\N
59	4					\N	\N
60	3	*Web Server SSL				\N	\N
60	4					\N	\N
61	3	*Web Auth Scanner				\N	\N
61	4					\N	\N
62	3	*Web Directory Scanner				\N	\N
62	4					\N	\N
63	3	*Web File Scanner				\N	\N
63	4					\N	\N
33	3	*DNS AFXR	\N	\N	\N	\N	\N
33	4	\N	\N	\N	\N	\N	\N
64	3	Non Automated	\N	\N	\N	\N	\N
64	4	\N	\N	\N	\N	\N	\N
1	3	DNS A	Checks DNS A records for the domain.	Not too much impact.	\N	Test Reference	Question Test
1	4	Deutsche DNS A	\N	\N	\N	Eine	Kleine
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
14	17	t	1	0	0	0	0
14	3	t	16	13	0	0	0
15	3	t	16	9	0	0	0
16	3	t	16	1	1	0	0
12	3	t	16	0	0	0	0
17	3	t	16	2	0	0	0
14	14	t	4	3	0	0	0
3	19	t	1	0	0	0	0
3	3	t	16	16	1	0	0
14	18	t	1	0	0	0	0
17	16	t	18	8	0	0	0
14	15	t	3	2	0	0	0
15	16	t	18	9	0	1	0
14	16	t	18	13	0	0	0
\.


--
-- TOC entry 2020 (class 0 OID 16530)
-- Dependencies: 165
-- Data for Name: target_check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_inputs (target_id, check_input_id, value, file, check_id) FROM stdin;
3	25	onexchanger.com	f87e2840daf20e67a4093b4679eacdf27b14355c478abd944a48cbaed56e7ddc	24
3	23	onexchanger.com	a47f60a2168e7c9f57a48d750573a46839e9d879dda1a0b4b797ff7488a2394f	22
14	42	10	69328c077bbeb65a89337439411d58c3ced398270dda8664da62e1235a864040	53
14	43	source@gmail.com	bf0b5a000f1c6b7694d0db2e100c66cc03b46cf8b0ec6cc0e5e790c7be4c3eda	53
14	44	destination@gmail.com	612877ba5a10842a4155e8213ae8c3d198ed7d20354d6af1e38dacb459eb126c	53
3	8	no	4442a6f24493fe3fbed7f364a4a7404e92080ee51736779dbe02544e228ffe2d	23
15	52	index.php\nstyle.css\ncss/style.css\nfavicon.ico\nrobots.txt	\N	63
14	23	\N	44829fcfa89ede72703945859b81724e72a92da169c978bec4bec75da248a197	22
3	32	0	9791d4369ee0c61519a7b0816779a99d4a366053265871f4fd757d58662a5496	39
14	40	1-80	661c023f4eaa177493426339f0d37e06f0a3c39dcca0e070b172dc99b60b56f9	52
14	32	0	c7c9c43e8e922b10d9570e5b7b597a0531bfe031411ffc522c13a007dc01811d	39
14	33	hello\nworld\nandrewdev	87ed81a0b5d2fbce1cbfe7bd1444df32053fdbefa1bdc83e8c536b5172f3c324	40
14	34	123\n456\nEdPIJOCq9CR3Hh7U9p114gQaU	7915461b9a2a5849d717b1a65e0c658592ae7f546b4f392d63523921f759c041	40
17	35	php	ddb7ad69ebfe972d403ef87bd3657ace23deee0270ae4ce81aa2b52ecc566912	46
3	29	0	f113fefd48859cdb63820fcb09a3200e57baa50e87493a1ef39d5afc3e3308a9	34
15	8	0	\N	23
15	19	php	\N	31
15	20		\N	31
15	21	100	\N	31
3	31	0	77c3a23e934c83348eb8d7702bebd96d16dc763d6f50bc9209891ed414a28fcb	35
15	51	admin/\nimages/\ncss/	f88b83ddb2174b5cef8af4e688b8c6f52c4a31d35ddb868b26f02bd5a51103f8	62
3	38	0	e03af5d403145bec5f60e0d3540fe7455285e6de7b145268198d2150e25d247c	47
14	45	root\njohn\nanton	2784cfbbcb4d1fde94a1fa58c804467f49213fe568ab3a7661e7aa96cec86031	54
14	46	123\n456\nf/xDQaTpzZAPx?C/@wY9NH4U9	082e9aee7717a7f023f6ed64fb828e27dc13f5edc15ac40d641a731b1eeceb57	54
3	22	onexchanger.com	\N	1
14	11	erbol.turburgaev@gmail.com	\N	28
17	36	10	2997289b683a446c2ed4ba0523f6f83932c8d64b926f5e61df5e73b924226a5c	47
17	37	100	9a28205ebe31bd382ef79e69a5165dfc6c920577433773d4ebd226e496a4e2ae	47
14	12	smtp.yandex.ru	\N	28
14	13	emailreminder@yandex.ru	\N	28
14	14	123321q	\N	28
17	38	1	c8c8974005a60b0341f9a8e1f2240bf5462bf10535b3662319bd334b3ce3e4cc	47
14	39	1\n80	1d64510e269bc0ae18935cd14791533ac799ce3aa92cebaedaec1eaa5664559d	51
14	15	emailreminder@yandex.ru	\N	28
14	8	1	1a504443dde46920c403758f2a2f7c00a553887111ea332a6baec47cd85a2431	23
14	16		\N	28
16	23		\N	22
14	22	0	\N	1
15	48	admin/\nindex.php\nstyle.css	287f70822e7c986a5ee74d99350773494ffd94ddc46208d0cb15f557f19bc288	56
15	49	10	81855d954b770e21b274b19648322397256753a8aac9c6008a09ee16450a6ae8	60
17	27	10	f91f34daf7005220687821b2c296eaff1ed81a5045b7233f0c45beec3fc4d40c	32
15	50	\nadmin/\nlist/	d3170c67c91e847733e48d7acc81f01ae6f2172e039ce565aa4950feca2a9665	61
14	36	10	a9b70bf90d5a60127557469730b2aaa2ecec312994d0d5ac7334b6ae72aeba5d	47
15	25	\N	81d8635c9ea48b68a590cd2f62c61b3b8ed596b89c54371e2d1805d0abe6191e	24
15	30	120	65b5d8781454e7bc5f478e0fa8d85232c87983fbcea89f5b418c93d5b57845b5	35
15	31	0	61b9e6b835822804e17b78fd52111f1c9e32baea93765179f239c669b1c1f272	35
15	22	0	a9e5873af169cbcd5991991e80a15586b8c133f411ba841db2fa2c88269dd8c2	1
15	36	10	b44ea935705cb748d29bdb8b941613ed452d57cf16db513e611e52b28da70c5e	47
15	37	100	ca3d90aaeb8693b9da3407b2cd750200c8f5b65a0a10b774d5ab4920f0c0f915	47
15	38	0	ea5e97ac2aab32f692228e2d0591a3764b4dbddf8c52149c5fc1a1eb2cf71a1b	47
14	37	100	727daa37e4ae5aea97734e15d21cf89e5ddcdbb290433fcef5789a8f4d0690f4	47
14	50	\N	beba376342ad4f62bdcd4b2e6c15ae197e855991733934914f8e7b71010e6615	61
14	30	120	966b09a3451e32c06df4778a4efe1377749ca7b3fe40d2800e62f53e4a1cd2a5	35
14	49	10	bfca32c413d997a8b2267823fd747357ab9c7365c0be7f22322e57e58180fb22	60
14	51	\N	ef2257146d25f80357139f1275e8fed3fdaa57f62ab57fc2bd25ee057767e3f2	62
14	38	0	d46e19c5a4fde928a67230ac6a48ee2934096156354f50b1fc99ea436e2759a9	47
14	31	0	ab134ef1bce2ee5dea9ff6d6f6d1f3c7d83b22c565d758b6e8c22076b09c5a6b	35
14	28	120	73d5a1734d705ddca4eaaa36fa42efe78a7eda1d504eabfbd3c162d5f7f8f712	34
14	29	0	b875c6b28f4bffceb7b297b0ef91849fea0323b1c2214a56ae33e6ed096ae64c	34
14	41	1000	fe002518a6b4961d2f969a1f57607eb739d113cba387af1c140c3f9d52ced21a	52
14	35	php	66a805bcf253ac67001a06168581eef91a9fda12ff4dfe0f742e187e30076e05	46
14	19	php	36f690a351ab61eca4b3f4cb684b75b21aabef5454e3fbdb9dc93ef0e7b13142	31
14	20	\N	9fdcf584215cf8c0a40ec2d896d8f91cf222bc33935d701f9f51093e4ef89ec5	31
14	21	100	040a89c6575b386e0e841218f2b55ae4ef364e2a2712943356e998f052eb79c6	31
14	48	rss/\nrss.xml\nrss\ncss/\ncss	541ea7c0d4d074e4a31c60736515e3145ebcee0b693188c962dc3d31e6a0900c	56
3	26	onexchanger.com	e14dc1e981be819b9fa070b1b05426d9761561abe92cbbdcd27183f47b1cf985	25
3	47	\N	13d6ef25bb2d0f715c509c1b0c5d486ca1d6eaf23a689ec607c5b90438b5d72d	55
3	28	120	2934c38764d11626a9f3a235c3a1933438f5bce46684e95c40c76aaea1b92917	34
3	30	120	6bd399bfa58015dd01987021fa0a7dad81e353ae9396e92ef8a28808e0609411	35
3	36	10	f973dda84bcac9ad783363061b60fc9a5f005f47d84c50d080ac7a13f3a617eb	47
3	37	100	951f65230c03f2ee91dfe5c848851c45445414d12b9ae0b6a4eed076134f3672	47
\.


--
-- TOC entry 2021 (class 0 OID 16536)
-- Dependencies: 166
-- Data for Name: target_check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_solutions (target_id, check_solution_id, check_id) FROM stdin;
\.


--
-- TOC entry 2022 (class 0 OID 16539)
-- Dependencies: 167
-- Data for Name: target_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_checks (target_id, check_id, result, target_file, rating, started, pid, status, result_file, language_id, protocol, port, override_target) FROM stdin;
14	46	\n\n->[+] Target : http://onexchanger.com/\n->[+] Basic c0de of the site : php\n->[+] Scanning control panel page...\n\n\n\n[+] http://onexchanger.com/administrator.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administrator/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/moderator/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/webadmin/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminarea/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/bb-admin/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminLogin/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin_area/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/panel-administracion/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/instadmin/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/memberadmin/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administratorlogin/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adm/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/account.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/index.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/admin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/account.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin_area/admin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin_area/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/siteadmin/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/siteadmin/index.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/siteadmin/login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/account.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/index.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/admin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin_area/index.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/bb-admin/index.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/bb-admin/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/bb-admin/admin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/home.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin_area/login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin_area/index.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/controlpanel.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admincp/index.asp \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admincp/login.asp \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admincp/index.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/account.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminpanel.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/webadmin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/webadmin/index.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/webadmin/admin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/webadmin/login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/admin_login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin_login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/panel-administracion/login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/cp.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/cp.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administrator/index.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administrator/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/nsw/admin/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/webadmin/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/admin_login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin_login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administrator/account.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administrator.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin_area/admin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/pages/admin/admin-login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/admin-login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin-login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/bb-admin/index.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/bb-admin/login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/bb-admin/admin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/home.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/modelsearch/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/moderator.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/moderator/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/moderator/admin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/account.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/pages/admin/admin-login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/admin-login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin-login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/controlpanel.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admincontrol.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/adminLogin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminLogin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/adminLogin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/home.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/rcjakar/admin/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminarea/index.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminarea/admin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/webadmin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/webadmin/index.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/webadmin/admin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/controlpanel.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/cp.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/cp.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminpanel.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/moderator.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administrator/index.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administrator/login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/user.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administrator/account.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administrator.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/modelsearch/login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/moderator/login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminarea/login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/panel-administracion/index.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/panel-administracion/admin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/modelsearch/index.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/modelsearch/admin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admincontrol/login.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adm/index.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adm.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/moderator/admin.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/user.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/account.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/controlpanel.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admincontrol.html \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/panel-administracion/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/wp-login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminLogin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin/adminLogin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/home.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/secureadmin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminarea/index.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminarea/admin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminarea/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/panel-administracion/index.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/panel-administracion/admin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/modelsearch/index.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/modelsearch/admin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admincontrol/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adm/admloginuser.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admloginuser.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin2.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin2/login.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin2/index.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adm/index.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adm.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/affiliate.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adm_auth.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/memberadmin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administratorlogin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/secureadmin.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/secureadmin/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/verysecure.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/securelogon.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin2009.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/webadministration/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/webadministrasi.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admininput.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/secure.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/secureadministration.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/phpmyadmin/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/sosecure.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/hardfound.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/dificultadmin.php/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administracion/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/root.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/locked.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/locked/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminnn.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminsitus.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminsitus/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminsite/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminsite.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administratorsite/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminpageonly/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/adminonly.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin-site.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/admin-site/ \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/administratorsite.php \n[!] status => 404 Not Found\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://onexchanger.com/usersite	cd5be42ba89a4b0ba9442ecac1ae03a254df260e221af4d787b7e0df2e11a103	\N	2012-06-29 13:30:13.940914	\N	finished	d72be78b02141925304976f471b0d7e85ee1ffcf162d936600171cdedf2374dc	3	http	\N	\N
3	22	46.4.98.106	ea463a27f065d4ed139f0d594c5c21aba43de98a243420cbf14df82302acfc0b	\N	2012-06-29 22:19:24.116848	\N	finished	9bacf7ddc210a725982d4957486a07f2e623322c9f83ee817466a2ff26f5c4e5	3	\N	\N	\N
14	26	220 smtp13.mail.yandex.net (Want to use Yandex.Mail for your domain? Visit http://pdd.yandex.ru)	7d513c98074f61eef4d4c984393ffe31c9c9cbad689167ccca7b5f63afb48289	info	2012-06-12 05:25:05.712931	12730	finished	b3760668672a8b5f1b31cde0ca3fa91688e0752dd6dbf1bbf284fc20f45c96e0	3	\N	\N	smtp.yandex.ru
14	52	\nStarting Nmap 5.00 ( http://nmap.org ) at 2012-06-29 11:30 MSK\nInteresting ports on static.106.98.4.46.clients.your-server.de (46.4.98.106):\nNot shown: 78 closed ports\nPORT   STATE SERVICE\n22/tcp open  ssh\n80/tcp open  http\n\nNmap done: 1 IP address (1 host up) scanned in 0.52 seconds\n	55e97dec22aab1f869e4d24e25561e167ccc0e6e76fb8486dbe9debd090b3106	\N	2012-06-29 11:30:14.196458	\N	finished	4d1321e0bece868d10dde821cba5c1e72ecdc3ae01754c40fcdc2dee5d172f65	3	\N	\N	\N
14	28	1.txt\nIOError: [Errno 2] No such file or directory: 'smtp_filter_files/Screenshot at 2012-04-11 16:24:25.png'	694d12d90e5d1ab60b1bd33ca3088b07271b1ae3c8667a0da85a4477830df8ca	info	2012-06-12 05:35:30.310166	12814	finished	29dc8d40545c6dc2c7bad2be1da7b9eeebd9fd296f02c93d82ffc726f1680d6c	3	\N	\N	
14	22	NoHostName: No host name specified.	2d10bf32b247c62a9b0e3647de84b7a28f3188fd4214182970cf2437ebd665a0	\N	2012-06-29 04:07:46.496844	25489	finished	903df9881c484cfafef2510c26434dcb6cc15e71ce92461ff4596ee2b3ee7dd0	3	\N	\N	\N
15	24	Nameserver                     IP                  SOA Serial      Refresh    Retry      Expire     Minimum\n-----------------------------------------------------------------------------------------------------------\ndns1.webdrive.ru               IP 213.189.213.54   SOA 1340275724  3h         60m        7d         24h\ndns2.webdrive.ru               IP 188.127.225.161  SOA 1340275724  3h         60m        7d         24h\nThe recommended syntax for serial number is YYYYMMDDnn (YYYY=year, MM=month, DD=day, nn=revision number), RFC 1912 (1340275724)\nThe recommended value for expire time is 2 to 4 weeks, RFC 1912 (7d)	b8d5a7885d1aa26f5b7c41cefe89640a208c5e7a232a466d193f694ff8d3258d	\N	2012-06-29 10:07:24.083583	27412	finished	d1d1974716cb59b9b40f6832c81d4dcc0e2b40fed30c5a7c5982cd8d72f0a20c	3	\N	\N	\N
3	1	46.4.98.106	7180d6e804c16df0ead52b262c4e9d4880debf39573e1dc06e1011a2200d9d97	low_risk	2012-06-30 19:53:01.675951	\N	finished	27358dbf1d992d127410dc5c50e66b862ae1ef757aa5580ae772fc8f82063e10	3	\N	\N	dns1.registrar-servers.com
3	38	Error: could not get NS record\n	5b3c23cf158df08575858621871a16c50671b1f03ef061cec32d252b1c674686	\N	2012-06-29 22:19:33.929704	\N	finished	eda22139efb136bffa237250f5a27ed7e247d3257c602128fb2c1b429cceb224	3	\N	\N	\N
14	61	No output.	cf7bec22328aa433873e92f3c0148ffecea3435d263bab4c8b2aa49789b86f6f	\N	2012-06-29 13:29:00.791586	\N	finished	20eb38c20c9872984af8d946ff850a7cd59245ff5fd7cfa990a2aa1f9c82523b	3	http	80	\N
3	25	DNS request timeout.\nNo SPF records.	119094ed2bf36ef432570ec00d4f901c7275b18931a8cd295c2807ef55d7eb6c	\N	2012-06-29 22:19:34.146213	\N	finished	c224d1752dbdcef5e0f3e9b362634d23209b6bde94f1eac902d73f63615d1857	3	\N	\N	\N
14	44	HTTP/1.1 200 OK\nConnection: close\nDate: Fri, 29 Jun 2012 09:31:23 GMT\nServer: nginx/1.1.19\nContent-Length: 14368\nContent-Type: text/html\nClient-Date: Fri, 29 Jun 2012 09:29:01 GMT\nClient-Peer: 46.4.98.106:443\nClient-Response-Num: 1\nClient-SSL-Cert-Issuer: /C=GB/ST=Greater Manchester/L=Salford/O=Comodo CA Limited/CN=PositiveSSL CA\nClient-SSL-Cert-Subject: /OU=Domain Control Validated/OU=PositiveSSL/CN=onexchanger.com\nClient-SSL-Cipher: DHE-RSA-AES256-SHA\nClient-SSL-Warning: Peer certificate not verified\nLink: </media/styles.css?8b05f5db8b866127cedd51951a35430d1925e900cc33bbb25708871fe5834f16>; rel="stylesheet"; type="text/css"\nLink: </media/images/favicon.png>; rel="icon"; type="image/png"\nSet-Cookie: session="kuC56OxhiDAlIR0MLgiGJpEGxHgrrK+ptIU5rrv/Y2U="; Path=/\nTitle: Home - One Exchanger\nX-Meta-Description: One Exchanger is a new electronic currency exchanger supporting the most modern electronic payment systems that can be found on the Internet.\nX-Meta-Google-Site-Verification: i639T41mI0p5ImEQq_RlH3S8s6X8-n_9eC1CqSFDuFw\nX-Meta-Keywords: exchange,exchanger,e-currency,digital currency,liberty reserve,perfect money,lr,pm,online\n\n<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\n<html xmlns="http://www.w3.org/1999/xhtml">\n<head>\n\\40\\40\\40\\40\n\\40\\40\\40\\40\n    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />\n    <title>Home - One Exchanger</title>\n    <meta name="description" content="One Exchanger is a new electronic currency exchanger supporting the most modern electronic payment systems that can be found on the Internet." />\n    <meta name="keywords" conte...\n(+ 13856 more bytes not shown)\n\n	8ce5646a19672313491cee7510a37d4b439dbc6b45f36aa4f3c26c2f53a31187	\N	2012-06-29 13:29:01.615077	\N	finished	1e27a41c2ebd6330b69cb28b70d4af925e96c5c179e715cd5849f4dc08207aff	3	http	\N	\N
14	23	www.hesennergis.ch\nwww.kaesespycher.ch\nwww.monikabieri.ch\nwww.nadisna.ch\nwww.syrian-tourism.info\nwww.susanne-guler.ch\nwww.transilom.ch\nwww.hüäbi.ch\nwww.trächtigerinder.ch\nwww.zürcher-spielzeugmuseum.ch\nwww.livemusiker.ch\nweb255.login-14.hoststar.ch\nmail.livemusiker.ch\nwww.scogbasel.ch\nchakavak.ch\nwww.fruttati.ch\nmissch.cessodelvicolo.ch\nwww.zuercher-spielzeugmuseum.ch\ngalerie.goodflying.ch\nms-oberwallis.ch\nwww.equazen.ch\nwww.ms-oberwallis.ch\nmail.fruttati.ch\nmail.zuercher-spielzeugmuseum.ch\nmail.scogbasel.ch\nmail.equazen.ch\nmail.ms-oberwallis.ch\nwww.chakavak.ch\nmail.aclasheinzenberg.ch\nwww.5izug.ch\nmail.5izug.ch\nwww.seeweb.ch\nmail.seeweb.ch\nchky.ch\npb.scbluestars.ch\nwica.awiweb.ch\nwww.musikundklang.ch\nmail.musikundklang.ch\nwww.fairytech.ch\nwww.pokernight.ch\nfairytale.ch\nmail.fairytech.ch\nmail.pokernight.ch\nwww.bigzis.ch\nns27.hoststar.ch\nlukasanrig.ch\nweb252.login-14.hoststar.ch\nwww.4nb.ch\nwww.drue3.ch\nwww.spettacoli29.ch\ngatesports.ch\nkinderwerkstatt-holzwurm.ch\nwww.alwe-consulting.ch\nwww.anitas-traumwelt.ch\nwww.arabische-kulturecke.ch\nwww.beechgrove.ch\nwww.boess.ch\nwww.chemistery.ch\nwww.chragokyberneticks.ch\nwww.compurama.ch\nwww.computeria-oerlikon.ch\nwww.diefloristen.ch\nwww.elektro-team.ch\nwww.elternbeirat.ch\nwww.fa-kontaktgruppe.ch\nwww.good-feeling.ch\nwww.goodflying.ch\nwww.gordon-pointer.ch\nwww.gospel-singers-ruemlang.ch\nwww.groovinbrass.ch\nwww.gschpaessig.ch\nwww.herag.ch\nwww.holistikum.ch\nwww.industria-biennensis.ch\nwww.kaufmann-modellbahnen.ch\nwww.keytop.ch\nwww.khmer.ch\nwww.kirche-hasle.ch\nwww.kita-coccinella.ch\nwww.matterhorn-web.ch\nwww.medazzarock.ch\nwww.mediasol.ch\nwww.mitradevi.ch\nwww.my-angel.ch\nwww.netprotect.ch\nwww.nuplan.ch\nwww.pigmento.ch\nwww.quelleonline.ch\nwww.realsilk.ch\nwww.ref-frick.ch\nwww.rennerag.ch\nwww.rentasecretary.ch\nwww.rhy-gusler.ch\nwww.richivogt.ch\nwww.roethlisberger-zimmerei.ch\nwww.s-motion.ch\nwww.scchristiania.ch\nwww.shever.ch\nwww.sidelen-huette.ch\nwww.simonmaurer.ch\nwww.skywings.ch\nwww.smileconcept.ch\nwww.sprachenservice.ch\nwww.stempelgarten.ch\nwww.swiss-light.ch\nwww.systema-swiss.ch\nwww.tambourenfzr.ch\nwww.taverne-athena.ch\nwww.theater-sempach.ch\nwww.theater-spektakel.ch\nwww.traechtigerinder.ch\nwww.volvostall.ch\nwww.vwclub.ch\nwww.wilervs.ch\nwww.zuberbuehler-dental.ch\nwww.aikidoschulebern.ch\nwww.vettepiratemodelcars.ch\n5sterne-review.bymyself.ch\nwww.stpauly.ch\nwww.gligge.ch\nwww.buchmann-schreinerei.ch\nwww.malatelier-blandastoop.ch\nmail.industria-biennensis.ch\nmail.my-angel.ch\nmail10.netprotect.ch\npostman.netprotect.ch\nmail.gatesports.ch\nwww.berufsfotos.ch\nwww.lagotto-romagnolo.ch\nmail.richivogt.ch\nmail.roethlisberger-zimmerei.ch\nmail.rhy-gusler.ch\nwww.zentrumroessli.ch\nmail.rentasecretary.ch\nmail.rennerag.ch\nmail.ref-frick.ch\nmail.realsilk.ch\nmail.scchristiania.ch\nwww.otto-georg-linsi.ch\nmail.weisstannental.ch\nmail.wilervs.ch\nmail.pigmento.ch\nmail.zuberbuehler-dental.ch\nmail.volvostall.ch\nmail.vwclub.ch\nmail.shever.ch\nmail.sidelen-huette.ch\nmail.simonmaurer.ch\nmail.skywings.ch\nmail.smileconcept.ch\nmail.s-motion.ch\nmail.spettacoli29.ch\nmail.sprachenservice.ch\nmail.stempelgarten.ch\nmail.swiss-light.ch\nmail.systema-swiss.ch\nmail.taverne-athena.ch\nmail.theater-sempach.ch\nmail.theater-spektakel.ch\nmail.traechtigerinder.ch\nmail.nuplan.ch\nmail.mitradevi.ch\nwww.fsg-mels.ch\nwww.bahnmuseum-kerzers.ch\nmail.mediasol.ch\nmail.medazzarock.ch\nmail.vettepiratemodelcars.ch\nmail.bymyself.ch\nwww.solveig.ch\nmail.gligge.ch\nmail.buchmann-schreinerei.ch\nmail.aikidoschulebern.ch\nmail.malatelier-blandastoop.ch\nmail.stpauly.ch\nwww.kehrli.ch\nwww.lukasanrig.ch\nwww.mg-uettligen.ch\nwww.otto-kehrli.ch\nwww.rotte32.ch\nwww.ns1.netprotect.ch\nwww.ns2.netprotect.ch\nwww.r-r-c.ch\nwww.corporalis.ch\nwww.frauenschwingclub.ch\nanitas-traumwelt.ch\nwww.siciliabedda.ch\nwww.helico-revue.ch\nerb-liquidationen.ch\nwww.eduaid.ch\nwww.kamin-und-ofen.ch\nwww.klaus-wilde.ch\n4nb.ch\nmedazzarock.ch\nwww.nomadi.ch\nmail.nomadi.ch\nwww.chky.ch\nmail.chky.ch\nwww.scbluestars.ch\nmail.scbluestars.ch\nwww.nano-maxi.ch\nmail.nano-maxi.ch\nwww.vandalism.ch\nmail.vandalism.ch\ntom.rotte32.ch\nweb224.login-14.hoststar.ch\nwww.patepat.ch\nmail.patepat.ch\nmail.hobi-forst.ch\nwww.funknstein.ch\nmail.funknstein.ch\nwww.erlbacher.ch\nmail.erlbacher.ch\nwww.jungschar-thurnen.ch\nmail.jungschar-thurnen.ch\nwww.zuenter.ch\nmail.zuenter.ch\nwww.bollywoodnight.ch\nmail.bollywoodnight.ch\nwww.rollsportbaden.ch\nmail.rollsportbaden.ch\nwww.nuessler-brunnen-ingenbohl.ch\nmail.nuessler-brunnen-ingenbohl.ch\nwww.der-wegweiser.ch\nmail.der-wegweiser.ch\nwww.shangrila-tibet.ch\nmail.shangrila-tibet.ch\nwww.thomasbrodmann.ch\nmail.thomasbrodmann.ch\nwww.spatzekafi.ch\nmail.spatzekafi.ch\ngordon-pointer.ch\nwww.bordercollierescue.ch\nmail.bordercollierescue.ch\nwww.seeblick-sempach.ch\nmail.seeblick-sempach.ch\nwww.awiweb.ch\nmail.awiweb.ch\nwww.waldfux.gemeinsamstark.ch\nmail.gemeinsamstark.ch\nwww.fechten-zuerich.ch\nmail.fechten-zuerich.ch\nwww.compusafe.ch\nmail.compusafe.ch\nwww.modellguss.ch\nmail.modellguss.ch\nchragokyberneticks.ch\nwww.seifenkistenrennen.ch\nmail.seifenkistenrennen.ch\nwww.asyllinks.ch\nwww.finel.ch\nmail.finel.ch\nwww.aegyptischertanz.ch\nmail.aegyptischertanz.ch\nwww.bsc04.ch\nmail.bsc04.ch\nwww.gemeinsamstark.ch\nwww.riedo-gipserei.ch\nmail.riedo-gipserei.ch\nwww.reithofer.ch\nmail.reithofer.ch\nwww.oechslin-malerei.ch\nmail.oechslin-malerei.ch\nwww.r-und-p.ch\nmail.r-und-p.ch\nwww.tmproductions.ch\nmail.tmproductions.ch\nmail.eduaid.ch\nwww.fairystyle.ch\nmail.fairystyle.ch\nmail.kamin-und-ofen.ch\nwww.tmproductions.info\nwww.schnickelschnack.gemeinsamstark.ch\nwww.tm1.ch\nmail.tm1.ch\nwww.khong.ch\nmail.khong.ch\nwww.miroflex.ch\nmail.miroflex.ch\nwww.arrow-group.ch\nmail.arrow-group.ch\naikidoschulebern.ch\nwww.gatesports.ch\nnetprotect.ch\ntux27.hoststar.ch\nwww.www.netprotect.ch\nwww.abo-online.ch\nmail.abo-online.ch\nwww.acr-cons.ch\nmail.acr-cons.ch\nmail.tmproductions.info\nwww.alwe.ch\nmail.alwe.ch\nwww.baldeagle.ch\nmail.baldeagle.ch\nwww.besteshirts.ch\nmail.besteshirts.ch\nwww.bfverlag.ch\nmail.bfverlag.ch\nwww.black-and-more.ch\nmail.black-and-more.ch\nwww.braunivillage.ch\nmail.braunivillage.ch\nwww.bueroruedi.ch\nmail.bueroruedi.ch\nwww.bymyself.ch\nwww.caimano.ch\nmail.caimano.ch\nwww.cessodelvicolo.ch\nmail.cessodelvicolo.ch\nwww.ctec.ch\nmail.ctec.ch\nwww.dexterrinder.ch\nmail.dexterrinder.ch\nwww.dks.ch\nmail.dks.ch\nwww.duranovic.ch\nmail.duranovic.ch\nwww.eggenbergers.ch\nmail.eggenbergers.ch\nwww.eisbaeren.ch\nmail.eisbaeren.ch\nwww.elmi.ch\nmail.elmi.ch\nwww.erb-liquidationen.ch\nwww.exovap-team.ch\nmail.exovap-team.ch\nwww.e-audio.ch\nmail.e-audio.ch\nwww.fairytale.ch\nmail.fairytale.ch\nwww.fechten-zürich.ch\nmail.fechten-zürich.ch\nwww.filot.ch\nmail.filot.ch\nwww.freaza.ch\nmail.freaza.ch\nwww.friitig.ch\nmail.friitig.ch\nwww.gaby-schifferle.ch\nmail.gaby-schifferle.ch\nwww.gdasen.ch\nmail.gdasen.ch\nwww.golf-fred.ch\nmail.golf-fred.ch\nwww.gorgen.ch\nmail.gorgen.ch\nwww.hochprozent.ch\nmail.hochprozent.ch\nwww.hotgate.ch\nmail.hotgate.ch\nwww.hube.ch\nmail.hube.ch\nwww.huggler-ag.ch\nmail.huggler-ag.ch\nwww.i-ts.ch\nmail.i-ts.ch\nwww.jlima.ch\nmail.jlima.ch\nwww.joanna-mod.ch\nmail.joanna-mod.ch\nwww.jubla-uedlige.ch\nmail.jubla-uedlige.ch\nwww.kasper-kuechen.ch\nmail.kasper-kuechen.ch\nwww.kaufmann-modellbau.ch\nmail.kaufmann-modellbau.ch\nwww.kinderwerkstatt-holzwurm.ch\nwww.kmu-beratung.ch\nmail.kmu-beratung.ch\nwww.kmu-services.ch\nmail.kmu-services.ch\nwww.kurzbettli.ch\nwww.lapalma-hairstudio.ch\nmail.lapalma-hairstudio.ch\nwww.laperla-trading.ch\nmail.laperla-trading.ch\nwww.lebensgefuehl.ch\nmail.lebensgefuehl.ch\nwww.lebensgefühl.ch\nmail.lebensgefühl.ch\nwww.louis-pretat-sa.ch\nmail.louis-pretat-sa.ch\nwww.marcmorant.ch\nmail.marcmorant.ch\nwww.marqant.ch\nmail.marqant.ch\nwww.mayas-collagen.ch\nmail.mayas-collagen.ch\nwww.metzgerei-eigenmann.ch\nmail.metzgerei-eigenmann.ch\nwww.miderecho.ch\nmail.miderecho.ch\nwww.mindernet.ch\nmail.mindernet.ch\nwww.mirjamundreto.ch\nmail.mirjamundreto.ch\nwww.mroesle.ch\nmail.mroesle.ch\nwww.nanomaxi-produkte.ch\nmail.nanomaxi-produkte.ch\nwww.noghreh.ch\nmail.noghreh.ch\nwww.nova-garage.ch\nmail.nova-garage.ch\nwww.oberos.ch\nmail.oberos.ch\nwww.officeplus-gmbh.ch\nmail.officeplus-gmbh.ch\nwww.partytorten.ch\nmail.partytorten.ch\ngegentrend.org\nschoggi.org\nmail.gegentrend.org\nmail.schoggi.org\nwww.physio-schiesser.ch\nmail.physio-schiesser.ch\nwww.porada.ch\nmail.porada.ch\nwww.primeliwaeg.ch\nmail.primeliwaeg.ch\nwww.primeliwäg.ch\nmail.primeliwäg.ch\nwww.proimedia.ch\nmail.proimedia.ch\nwww.raks.ch\nmail.raks.ch\nwww.rememberag.ch\nmail.rememberag.ch\nwww.rothwald.ch\nmail.rothwald.ch\nwww.ruplihufschmid.ch\nmail.ruplihufschmid.ch\nwww.gsell.biz\nwww.sconic.ch\nmail.sconic.ch\nwww.streetbeetles.ch\nmail.streetbeetles.ch\nwww.studio-delfin.ch\nmail.studio-delfin.ch\nwww.tarik.ch\nmail.tarik.ch\nwww.taucon.ch\nmail.taucon.ch\nwww.thetigers.ch\nmail.thetigers.ch\nwww.tifashion.ch\nmail.tifashion.ch\nwww.timeless-kosmetik.ch\nmail.timeless-kosmetik.ch\nwww.tschivi.ch\nmail.tschivi.ch\nwww.tvtrub.ch\nmail.tvtrub.ch\nwww.veljaca.ch\nmail.veljaca.ch\nwww.wandinger.ch\nmail.wandinger.ch\nwww.wernermeierluzern.ch\nmail.wernermeierluzern.ch\nwww.wohnart.ch\nmail.wohnart.ch\nwww.froeschl-autozubehoer.de\nmail.froeschl-autozubehoer.de\nwww.zone3000.ch\nmail.zone3000.ch\nwww.zwahlen-metallbau.ch\nmail.zwahlen-metallbau.ch\nwww.4hands.ch\nmail.4hands.ch\nwww.4-men.ch\nmail.4-men.ch\nelmi.ch\ndks.ch\nabo-online.ch\nctec.ch\nshop.stempelgarten.ch\nwww.ifon.ch\nmail.ifon.ch\nwww.3266.ch\nwww.candyfromastranger.ch\nsimon.staub.be\nmail.staub.be\nwww.lamm-bock.de\nwww.freaza.com\nwww.hundefreunde.li\nmail.lamm-bock.de\nwww.kehrli.info\nmail.kehrli.info\nwww.helico-revue.de\nmail.helico-revue.de\npeaceorfuck.com\nheli-revue.com\nmail.heli-revue.com\nrosenthaler.com\nwww.belline2.com\nwww.aerobatic-lady.com\nwww.altes-zollhaus-birnbaum.com\nwww.belline.com\nwww.bigzis.com\nwww.fairytale-hds.com\nwww.flamtau.com\nwww.grindracing.com\nwww.rosenthaler.com\nwww.zuberbuehler.com\nmail.altes-zollhaus-birnbaum.com\nmail.rosenthaler.com\nmail.zuberbuehler.com\nwww.aerobatic-sport.com\nwww.arabic-culture-corner.com\nwww.born1965.com\nwww.christiandecalvairac.com\nwww.est-world.com\nwww.matthias-stuber.com\nwww.palmenparadies-costablanca.com\nwww.pistorio.com\nwww.siruppiddy.com\nwww.tai-chi-qi-gong.com\nwww.mission4you.com\nwww.peaceorfuck.com\nwww.yachtbelline.com\naerobatic-lady.com\naerobatic-sport.com\narabic-culture-corner.com\naltes-zollhaus-birnbaum.com\nbelline.com\nbelline2.com\nbigzis.com\nborn1965.com\nfairytale-hds.com\nflamtau.com\nfreaza.com\ngrindracing.com\nhelico-revue.com\n	e2a8a8639beabf62aca263a2f38f0f8f1e6e8fde947ac4f2a9eaad2b8ed73779	\N	2012-07-07 15:20:39.027246	\N	finished	79234fbcdd9ece289003c0eb9f99185b454b82b9ecedb66f7ffeaab6a9038fcb	4	\N	\N	netprotect.ch
15	50	NS Bind version is "9.3.6-P1-RedHat-9.3.6-20.P1.el5_8.1"	910195037cbce1dae69d52d55048ab096fec77546e861f55ffe8a98acc3cff4c	\N	2012-06-29 10:07:23.707276	27408	finished	5397f7ed9ad509c5051d4a9c338726467f7a7836aeb5f207f83fad5af604dc4a	3	\N	\N	\N
3	55	\nChecking for wildcard DNS...\nNope. Good.\nNow performing 16 test(s)...\n\nSubnets found:\n	14147dcf6ed33e26306f929184baa4a029b6e43bccb4b963c48878d0fc1d9ff0	\N	2012-06-29 22:19:28.197824	\N	finished	54b94a2e7bababb3c6fba210b9ee458b8ff11411a0b334b83295a5c648610eed	3	\N	\N	\N
14	43	1) https://onexchanger.com/en/\n2) https://onexchanger.com/en/account/signup/\n3) https://onexchanger.com/en/account/login/\n4) https://onexchanger.com/en/static/about/\n5) https://onexchanger.com/en/news/\n6) https://onexchanger.com/en/faq/\n7) https://onexchanger.com/en/static/affiliates/\n8) https://onexchanger.com/en/static/contact/\n9) https://onexchanger.com/en/dynamic/i18n.js\n10) https://onexchanger.com/en/static/discounts/\n11) https://onexchanger.com/en/static/rates/\n12) https://onexchanger.com/en/static/privacy/\n13) https://onexchanger.com/en/static/terms/\n14) https://onexchanger.com/en/account/promo/\n15) https://onexchanger.com/en/account/password-reset/\n16) https://onexchanger.com/en/account/\n17) https://onexchanger.com/en/account/transactions/\n18) https://onexchanger.com/en/account/referral-transactions/\n19) https://onexchanger.com/en/account/withdrawal-requests/\n20) https://onexchanger.com/en/account/settings/\n21) https://onexchanger.com/en/account/payment-details/\n22) https://onexchanger.com/en/account/logout/\n23) https://onexchanger.com/en/news/view/9/\n24) https://onexchanger.com/en/news/view/7/\n25) https://onexchanger.com/en/news/view/5/\n26) https://onexchanger.com/en/news/view/3/\n27) https://onexchanger.com/en/news/view/1/\n28) https://onexchanger.com/en/exchange/\n29) https://onexchanger.com/en/dynamic/currencies.js\n	87c80b05d4f4bd17ee817fb8a6d0c8faa5561e5bca3853568f8ade4bdaa78499	\N	2012-06-29 13:29:30.69028	\N	finished	0a8fe46ee2c09f978374f249db731216e39e38f4842de3b69b2790f2fda50c1d	3	http	\N	\N
15	30	Dangerous methods allowed: TRACE, PUT, DELETE.	e5a93ba7361c4a3ccba34a77730d001075875e2e98fb54e6cbf8acf578a08fc0	hidden	2012-06-12 09:19:15.11335	14511	finished	230cc8271566f26bb482b17d81e766f8b9c113a1e4c52c7f68fba52a0f12a08b	3	\N	\N	
15	31	No vulnerabilities detected.\nScanned 1 URLs.\n0.00 seconds elapsed.	753edbe0c96decc4d981e0cb1b8f77cd9d8b8779c16a08777794f88eb7114ab3	med_risk	2012-06-12 09:31:18.217071	14691	finished	3d551c5a78ed09106779c1cd1c2e5e15ece19fbc7c3374b0d9e77fe37f41f694	3	\N	\N	onexchanger.com
16	22		\N	low_risk	\N	\N	finished	\N	3		\N	
15	38	Error: could not get NS record\n	a3ec1a0cd658fcb5cfd38d23f58ff171981fd9a937e710b19d897c6a49d79088	\N	2012-06-29 10:07:23.93352	27414	finished	4cfb336be1d175f5f8db9d7b461a5b59d43e16c54dbd0b5b9d530462dbce8f49	3	\N	\N	\N
15	35	\n1) gostrip.ru (108.175.157.152)\n\tdns1.webdrive.ru (213.189.213.54)\n\tdns2.webdrive.ru (188.127.225.161)\n	c6f575f1261039ef039991acc6cbdec201c94809087758a956e3f3f863ac2a6e	\N	2012-06-29 10:07:23.98789	27417	finished	1b5e1fb81cf9fece91ce0eb68553175d2da470cf870fddc09f03f2e648f06c9d	3	\N	\N	\N
17	32	[Warning] gtta.demonstratr.com: Host can accept more than 5 ranges.\n	04e444daf318bb1da390eb8d6ee82a4d13a37ba3a4f63d41d26392d0eda82e3f	\N	2012-06-27 05:48:07.590262	11262	finished	ff28ca2874ce464108d09aed4e401d8e15edaefa0a3cb0aaa25bb4ca73041cde	3	\N	\N	gtta.demonstratr.com
3	39	dns1.biz\t\t210.157.1.131\ndns1.cc\t\t199.30.89.207\ndns1.com\t\t209.68.51.4\ndns1.info\t\t204.13.162.123\ndns1.net\t\t50.19.102.80\ndns1.org\t\t98.124.199.1\ndns1.ws\t\t64.62.153.174\n	c493f2de6a0a003d1d1fa82d52ab2caa0b5cb92d72e611d8f1eacc03d2ed59d8	\N	2012-06-29 22:19:28.392447	\N	finished	9b3fa10ad61176ba56fadae158e11b22affe30b97dae962f7c0c9a3912e072a5	3	\N	\N	\N
3	23	No host names found.	a3b39744a1756b9f08cd65ddbebe6793b34a88d7c9fd15df1bfc1097cc05580c	\N	2012-06-29 22:19:30.067281	\N	finished	14bfb3a9e415100ac41fe77723fe4707c9ced78727275b905a5a4c3ca1815a4a	3	\N	\N	netprotect.ch
14	45	Killed	d4d9e1328c7bd39c95164dfd5363a16b967e3d5b8a3a850a0795b07b5825d205	\N	2012-06-29 13:30:13.931514	\N	finished	2b45a5a32fe193a4779560cec82c67a60adf8e447275ec17755a217fab37892d	3	http	\N	\N
15	58	**** runnning  Error begging scanner ****\n+ Error page found  --  \r \r \r Запрашиваемая страница не найдена!\r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r 0 товар(а/ов) - 0.00 руб\r \r \r \r \r \r \r \r Добро пожаловать! Вы можете войти как постоянный покупатель или зарегистрироваться. \r ГлавнаяЗакладки (0)Моя информацияКорзина покупокОформление заказа\r \r \r \r Обувь\r \r Корсеты\r \r Платья и прочее\r \r Тематические костюмы\r \r Стикини\r \r Аксессуары\r \r \r \r \r \r \r Главная\r &raquo; Запрашиваемая страница не найдена!\r \r Запрашиваемая страница не найдена!\r Запрашиваемая страница не найдена!\r \r Продолжить\r \r \r \r Информация О нас Оплата Доставка Служба поддержки Связаться с нами Возврат товара Карта сайта Дополнительно Производители (бренды) Подарочные сертификаты Партнёрская программа Акции Личный Кабинет Личный Кабинет История заказов Закладки Рассылка   Интернет-магазин GoStrip &copy; 2012 Работает на Opencart | Дизайн OpencartStuff.com &nbsp; \n\n+ Error page found  --  \r \r \r Запрашиваемая страница не найдена!\r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r \r 0 товар(а/ов) - 0.00 руб\r \r \r \r \r \r \r \r Добро пожаловать! Вы можете войти как постоянный покупатель или зарегистрироваться. \r ГлавнаяЗакладки (0)Моя информацияКорзина покупокОформление заказа\r \r \r \r Обувь\r \r Корсеты\r \r Платья и прочее\r \r Тематические костюмы\r \r Стикини\r \r Аксессуары\r \r \r \r \r \r \r Главная\r &raquo; Запрашиваемая страница не найдена!\r \r Запрашиваемая страница не найдена!\r Запрашиваемая страница не найдена!\r \r Продолжить\r \r \r \r Информация О нас Оплата Доставка Служба поддержки Связаться с нами Возврат товара Карта сайта Дополнительно Производители (бренды) Подарочные сертификаты Партнёрская программа Акции Личный Кабинет Личный Кабинет История заказов Закладки Рассылка   Интернет-магазин GoStrip &copy; 2012 Работает на Opencart | Дизайн OpencartStuff.com &nbsp; \n\n	be316e19f368c98993de0a2e326a7ce4c4334af7a0dd0877d7c16d0ff1769a64	\N	2012-06-28 00:06:22.900146	18759	finished	04b70cde8b184c875a71bcf062c572bb0606ea2cbff665fb6ce8b3fd35f9c150	3	\N	\N	\N
14	27	Server is not listed in any known blocklist.	afc4d8a54761bcda26dd083c40d50911b0912946412e56e8d60f4f25a69a231f	info	2012-06-12 05:32:50.887047	12788	finished	cc69e6ed1a72e861f726cb156a1fb89229cb954efba83b603ad7eafb65d6088b	3		\N	masadata.com
15	23	\N	\N	info	\N	\N	finished	\N	3	\N	\N	\N
3	48	report for dns1.registrar-servers.com (68.233.250.45)\n#\n# Query terms are ambiguous.  The query is assumed to be:\n#     "n + 69.16.244.25"\n#\n# Use "?" to get help.\n#\n\n#\n# The following results may also be obtained via:\n# http://whois.arin.net/rest/nets;q=69.16.244.25?showDetails=true&showARIN=false&ext=netref2\n#\n\nNetRange:       69.16.192.0 - 69.16.255.255\nCIDR:           69.16.192.0/18\nOriginAS:       AS32244\nNetName:        LIQUIDWEB-4\nNetHandle:      NET-69-16-192-0-1\nParent:         NET-69-0-0-0-0\nNetType:        Direct Allocation\nRegDate:        2005-05-26\nUpdated:        2012-02-24\nRef:            http://whois.arin.net/rest/net/NET-69-16-192-0-1\n\n\nOrgName:        Liquid Web, Inc.\nOrgId:          LQWB\nAddress:        4210 Creyts Rd.\nCity:           Lansing\nStateProv:      MI\nPostalCode:     48917\nCountry:        US\nRegDate:        2001-07-20\nUpdated:        2011-07-18\nRef:            http://whois.arin.net/rest/org/LQWB\n\nReferralServer: rwhois://rwhois.liquidweb.com:4321\n\nOrgAbuseHandle: ABUSE551-ARIN\nOrgAbuseName:   Abuse\nOrgAbusePhone:  +1-800-580-4985 \nOrgAbuseEmail:  abuse@liquidweb.com\nOrgAbuseRef:    http://whois.arin.net/rest/poc/ABUSE551-ARIN\n\nOrgTechHandle: IPADM47-ARIN\nOrgTechName:   IP Administrator\nOrgTechPhone:  +1-800-580-4985 \nOrgTechEmail:  ipadmin@liquidweb.com\nOrgTechRef:    http://whois.arin.net/rest/poc/IPADM47-ARIN\n\n#\n# ARIN WHOIS data and services are subject to the Terms of Use\n# available at: https://www.arin.net/whois_tou.html\n#\n	1c4cda5ab0def3d672a16e725837c79a980193460221c0ee50b0c12c35e11075	\N	2012-06-29 22:19:31.819171	\N	finished	e284a6e58bd5a0d2c3bea922a6f7c099b5b95414174dfda20863e1ebf00192a9	3	\N	\N	\N
17	43	1) http://gtta.demonstratr.com/login\n2) http://gtta.demonstratr.com/css/bootstrap.css\n3) http://gtta.demonstratr.com/css/bootstrap.datepicker.css\n4) http://gtta.demonstratr.com/css/style.css\n5) http://gtta.demonstratr.com/js/jquery.js\n6) http://gtta.demonstratr.com/a\n7) http://gtta.demonstratr.com/js/jquery.cookie.js\n8) http://gtta.demonstratr.com/js/bootstrap.js\n9) http://gtta.demonstratr.com/js/bootstrap.datepicker.js\n10) http://gtta.demonstratr.com/js/system.js\n11) http://gtta.demonstratr.com/app/l10n.js\n12) http://gtta.demonstratr.com/images/languages/en.png\n13) http://gtta.demonstratr.com/images/languages/de.png\n14) http://gtta.demonstratr.com/images/loading.gif\n	cf84bb76629f9fecc0202bef9863e0d5501eebecdced4a07b04d168eec0dc415	\N	2012-06-27 16:25:11.158717	15001	finished	47935195826c9b35f6b07d79c39ee7fb7476cc1fcd081e26ffadc187442558ff	3	\N	\N	\N
3	24	Nameserver                     IP                  SOA Serial      Refresh    Retry      Expire     Minimum\n-----------------------------------------------------------------------------------------------------------\ndns2.registrar-servers.com     IP 208.64.122.242   SOA ? (Timeout)\ndns3.registrar-servers.com     IP 188.138.96.213   SOA 2011121800  2h 46m 41s 30m 1s     7d 1s      1h 1s\ndns4.registrar-servers.com     IP 184.171.163.91   SOA 2011121800  2h 46m 41s 30m 1s     7d 1s      1h 1s\ndns5.registrar-servers.com     IP 72.20.38.137     SOA 2011121800  2h 46m 41s 30m 1s     7d 1s      1h 1s\ndns1.registrar-servers.com     IP 50.7.230.26      SOA 2011121800  2h 46m 41s 30m 1s     7d 1s      1h 1s\nThe recommended value for expire time is 2 to 4 weeks, RFC 1912 (7d 1s)\nThe recommended value for minimum TTL is 1 to 5 days, RFC 1912 (1h 1s)	bad976928bfff36fa820e5d953346cfb97c85c0a6bc4f2a15d58da57ba02b815	\N	2012-06-29 22:19:37.059927	\N	finished	ede50680d453c6d52e25ccbfe13c7b48f4ccee9f37e294340c2e535557ad6ef6	3	\N	\N	\N
15	36	\N	\N	info	\N	\N	finished	\N	3	\N	\N	\N
14	60	No output.	161575c605f832f5a99dab181453df460db10a37e1dd08931448793d2975f2b9	\N	2012-06-29 13:29:02.754818	\N	finished	2b11f7ce9c840be258b9eece5dd61ac78af3c80335330becf635f2a7ce8890d1	3	\N	\N	\N
3	36	Can't call method "ip" on an undefined value at dns_ip_range.pl line 22.	32c4243fe4a9eee03a86f1fed5f179749766435ebbfa3e730702506cdf0bb193	\N	2012-06-29 22:19:24.499927	\N	finished	ecd6e2b14bfcd924130de1f400a6abfb47d658363998d9f73018ef96ea8ffd65	3	\N	\N	\N
14	57	**** running cms plugin detection scanner ****\n+ CMS Plugins takes awhile....\n**** running Web Service scanner ****\n+ Web service Found: site uses google analytics\n	7d4c690ecdc515fec79e3bfd806cb04292f717e37f07b4ec76c7f5fbb8f1388c	\N	2012-06-29 12:58:41.394649	\N	finished	9ab12e2bd07d81513686c0ecfad7aefd39331db34071c617703307e3351c4a19	3	\N	\N	\N
3	34	\n1) Warning: no MX records for domain dns1.registrar-servers.com (NOERROR)\n	bd9b8c5b5281b2bc5017bed8a266b0b95a1f022cab0d27a8abb991a972b1b278	\N	2012-06-29 22:19:24.48819	\N	finished	c0eae9397d251a3f67f0852db027bf68e7cc3c1effeb2f824512ac493414a044	3	\N	\N	\N
14	59	Killed	86a4d75f24873fd5ec8e3ba518c7d5abb4fad7c929ba2b4bcc6218bed74b06a3	\N	2012-06-29 13:30:03.957396	\N	finished	f66ff1c89e8d99c6eac4f2453a42ab5cb6b4059f8f1af31d1cd071c6688d7f45	3	\N	\N	\N
14	58	CDbCommand failed to execute the SQL statement: SQLSTATE[22021]: Character not in repertoire: 7 ERROR:  invalid byte sequence for encoding "UTF8": 0xa9\nHINT:  This error can also happen if the byte sequence does not match the encoding expected by the server, which is controlled by "client_encoding".. The SQL statement executed was: UPDATE "target_checks" SET "target_id"=:yp0, "check_id"=:yp1, "result"=:yp2, "target_file"=:yp3, "rating"=:yp4, "started"=NOW(), "pid"=:yp5, "status"=:yp6, "result_file"=:yp7, "language_id"=:yp8, "protocol"=:yp9, "port"=:yp10, "override_target"=:yp11 WHERE "target_checks"."target_id"='14' AND "target_checks"."check_id"='58'	4dd2470a3b8f7ccb14cb4fb8058efe4bd1b0debb1d1ab88753b6cc987bf71cef	\N	2012-06-29 12:58:49.403131	\N	finished	e0727f32dd3807b9d0e21c025a75116feb17ffa58fba55893e51c82080f0810b	3	\N	\N	\N
15	60	Alternative names: www.onexchanger.com onexchanger.com\nValid from: Tue Dec 13 00:00:00 UTC 2011\nValid until: Wed Dec 12 23:59:59 UTC 2012 (expires in 5 months and 18 days)\nKey: RSA / 2048 bits\nSignature algorithm: SHA1withRSA\nServer Gated Cryptography: No\nWeak key (Debian): No\nIssuer: PositiveSSL CA\nChain length (size): 4 (4849 bytes)\nExtended Validation: No\nRevocation information: CRL, OCSP\nRevocation status: Good (not revoked)\n	d31b9b76485cf9c389fff25e8ecc8fbff5d620cfa80156e120bd7d3fc3d9545f	\N	2012-06-28 00:24:58.517396	19423	finished	e72339c642d55c49ae4fe3692bdb4f6186c512deaa0c87d533dd83a60f3d410b	3	\N	\N	onexchanger.com
14	29	Selected device eth0, address 192.168.1.10, port 34950 for outgoing packets\nTracing the path to onexchanger.com (46.4.98.106) on TCP port 80 (www), 30 hops max\n 1  192.168.1.1  1.149 ms  1.156 ms  0.903 ms\n 2  host-205-43.telecet.ru (81.22.205.43)  17.331 ms  17.093 ms  18.933 ms\n 3  172.29.155.43  18.212 ms  17.249 ms  17.354 ms\n 4  host-205-9.telecet.ru (81.22.205.9)  17.975 ms  16.918 ms  17.675 ms\n 5  ae-2.750.kazn-rgr1.pv.ip.rostelecom.ru (94.25.11.165)  17.604 ms  18.051 ms  16.709 ms\n 6  xe-2-1-0.frkt-ar2.intl.ip.rostelecom.ru (87.226.139.25)  96.567 ms\n    188.254.44.182  88.998 ms\n    xe-10-2-0.frkt-ar2.intl.ip.rostelecom.ru (87.226.133.242)  76.469 ms\n 7  decix-gw.hetzner.de (80.81.192.164)  103.272 ms  102.820 ms  102.333 ms\n 8  hos-bb1.juniper1.rz14.hetzner.de (213.239.240.246)  86.585 ms\n    hos-bb1.juniper2.rz14.hetzner.de (213.239.240.247)  92.871 ms\n    hos-bb1.juniper1.rz14.hetzner.de (213.239.240.246)  86.939 ms\n 9  hos-tr2.ex3k8.rz14.hetzner.de (213.239.224.169)  95.831 ms  96.588 ms\n    hos-tr4.ex3k8.rz14.hetzner.de (213.239.224.233)  102.040 ms\n10  static.106.98.4.46.clients.your-server.de (46.4.98.106) [open]  90.696 ms  89.538 ms  90.538 ms\n	0b19a84b8dfd8a1ab42b70f244770363af7c8f65bd0c188628de1d3599ac4567	info	2012-06-12 09:13:55.256531	14451	finished	bb8f7ba0d9a1bd5f1bf032ad8984aa0516b089c4017c6aec75f0b4687382bff3	3		80	
14	47	0nexchanger.com\nanexchanger.com\nbnexchanger.com\ncnexchanger.com\ndnexchanger.com\nenexchanger.com\nfnexchanger.com\ngnexchanger.com\nhnexchanger.com\ninexchanger.com\njnexchanger.com\nknexchanger.com\nlnexchanger.com\nmnexchanger.com\nnexchanger.com\nnnexchanger.com\nnoexchanger.com\no-nexchanger.com\noaexchanger.com\nobexchanger.com\nocexchanger.com\nodexchanger.com\noeexchanger.com\noenxchanger.com\noexchanger.com\nofexchanger.com\nogexchanger.com\nohexchanger.com\noiexchanger.com\nojexchanger.com\nokexchanger.com\nolexchanger.com\nomexchanger.com\non-exchanger.com\nonaxchanger.com\nonbxchanger.com\noncxchanger.com\nondxchanger.com\none-xchanger.com\noneachanger.com\nonebchanger.com\nonecchanger.com\nonechanger.com\nonecxhanger.com\nonedchanger.com\noneechanger.com\noneexchanger.com\nonefchanger.com\nonegchanger.com\nonehchanger.com\noneichanger.com\nonejchanger.com\nonekchanger.com\nonelchanger.com\nonemchanger.com\nonenchanger.com\noneochanger.com\nonepchanger.com\noneqchanger.com\nonerchanger.com\noneschanger.com\nonetchanger.com\noneuchanger.com\nonevchanger.com\nonewchanger.com\nonex-changer.com\nonexahanger.com\nonexbhanger.com\nonexc-hanger.com\nonexcaanger.com\nonexcahnger.com\nonexcanger.com\nonexcbanger.com\nonexccanger.com\nonexcchanger.com\nonexcdanger.com\nonexceanger.com\nonexcfanger.com\nonexcganger.com\nonexch-anger.com\nonexch4nger.com\nonexcha-nger.com\nonexchaager.com\nonexchaanger.com\nonexchabger.com\nonexchacger.com\nonexchadger.com\nonexchaeger.com\nonexchafger.com\nonexchager.com\nonexchagger.com\nonexchagner.com\nonexchahger.com\nonexchaiger.com\nonexchajger.com\nonexchakger.com\nonexchalger.com\nonexchamger.com\nonexchan-ger.com\nonexchanaer.com\nonexchanber.com\nonexchancer.com\nonexchander.com\nonexchaneer.com\nonexchanegr.com\nonexchaner.com\nonexchanfer.com\nonexchang-er.com\nonexchangar.com\nonexchangbr.com\nonexchangcr.com\nonexchangdr.com\nonexchange.com\nonexchangea.com\nonexchangeb.com\nonexchangec.com\nonexchanged.com\nonexchangee.com\nonexchangeer.com\nonexchangef.com\nonexchangeg.com\nonexchangeh.com\nonexchangei.com\nonexchangej.com\nonexchangek.com\nonexchangel.com\nonexchangem.com\nonexchangen.com\nonexchangeo.com\nonexchangep.com\nonexchangeq.com\nonexchanger.com\nonexchangerr.com\nonexchanges.com\nonexchanget.com\nonexchangeu.com\nonexchangev.com\nonexchangew.com\nonexchangex.com\nonexchangey.com\nonexchangez.com\nonexchangfr.com\nonexchangger.com\nonexchanggr.com\nonexchanghr.com\nonexchangir.com\nonexchangjr.com\nonexchangkr.com\nonexchanglr.com\nonexchangmr.com\nonexchangnr.com\nonexchangor.com\nonexchangpr.com\nonexchangqr.com\nonexchangr.com\nonexchangre.com\nonexchangrr.com\nonexchangsr.com\nonexchangtr.com\nonexchangur.com\nonexchangvr.com\nonexchangwr.com\nonexchangxr.com\nonexchangyr.com\nonexchangzr.com\nonexchanher.com\nonexchanier.com\nonexchanjer.com\nonexchanker.com\nonexchanler.com\nonexchanmer.com\nonexchanner.com\nonexchannger.com\nonexchanoer.com\nonexchanper.com\nonexchanqer.com\nonexchanrer.com\nonexchanser.com\nonexchanter.com\nonexchanuer.com\nonexchanver.com\nonexchanwer.com\nonexchanxer.com\nonexchanyer.com\nonexchanzer.com\nonexchaoger.com\nonexchapger.com\nonexchaqger.com\nonexcharger.com\nonexchasger.com\nonexchatger.com\nonexchauger.com\nonexchavger.com\nonexchawger.com\nonexchaxger.com\nonexchayger.com\nonexchazger.com\nonexchbnger.com\nonexchcnger.com\nonexchdnger.com\nonexchenger.com\nonexchfnger.com\nonexchgnger.com\nonexchhanger.com\nonexchhnger.com\nonexchinger.com\nonexchjnger.com\nonexchknger.com\nonexchlnger.com\nonexchmnger.com\nonexchnager.com\nonexchnger.com\nonexchnnger.com\nonexchonger.com\nonexchpnger.com\nonexchqnger.com\nonexchrnger.com\nonexchsnger.com\nonexchtnger.com\nonexchunger.com\nonexchvnger.com\nonexchwnger.com\nonexchxnger.com\nonexchynger.com\nonexchznger.com\nonexcianger.com\nonexcjanger.com\nonexckanger.com\nonexclanger.com\nonexcmanger.com\nonexcnanger.com\nonexcoanger.com\nonexcpanger.com\nonexcqanger.com\nonexcranger.com\nonexcsanger.com\nonexctanger.com\nonexcuanger.com\nonexcvanger.com\nonexcwanger.com\nonexcxanger.com\nonexcyanger.com\nonexczanger.com\nonexdhanger.com\nonexehanger.com\nonexfhanger.com\nonexghanger.com\nonexhanger.com\nonexhcanger.com\nonexhhanger.com\nonexihanger.com\nonexjhanger.com\nonexkhanger.com\nonexlhanger.com\nonexmhanger.com\nonexnhanger.com\nonexohanger.com\nonexphanger.com\nonexqhanger.com\nonexrhanger.com\nonexshanger.com\nonexthanger.com\nonexuhanger.com\nonexvhanger.com\nonexwhanger.com\nonexxchanger.com\nonexxhanger.com\nonexyhanger.com\nonexzhanger.com\noneychanger.com\nonezchanger.com\nonfxchanger.com\nongxchanger.com\nonhxchanger.com\nonixchanger.com\nonjxchanger.com\nonkxchanger.com\nonlxchanger.com\nonmxchanger.com\nonnexchanger.com\nonnxchanger.com\nonoxchanger.com\nonpxchanger.com\nonqxchanger.com\nonrxchanger.com\nonsxchanger.com\nontxchanger.com\nonuxchanger.com\nonvxchanger.com\nonwxchanger.com\nonxchanger.com\nonxechanger.com\nonxxchanger.com\nonyxchanger.com\nonzxchanger.com\nooexchanger.com\noonexchanger.com\nopexchanger.com\noqexchanger.com\norexchanger.com\nosexchanger.com\notexchanger.com\nouexchanger.com\novexchanger.com\nowexchanger.com\noxexchanger.com\noyexchanger.com\nozexchanger.com\npnexchanger.com\nqnexchanger.com\nrnexchanger.com\nsnexchanger.com\ntnexchanger.com\nunexchanger.com\nvnexchanger.com\nwnexchanger.com\nxnexchanger.com\nynexchanger.com\nznexchanger.com\n	bec2c43bbe201a1273e575c5d32217f7fa894d160b66f30cbd71e7331f308cff	\N	2012-06-29 04:07:46.581428	25496	finished	8f7b8ea0b01e28e02a07c635eae096d20688d50f484e273b8c82a45e97ebedd5	3	\N	\N	\N
14	34	\n1) onexchanger.com (46.4.98.106)\n\t1 ASPMX.L.GOOGLE.com (173.194.71.27)\n\t5 ALT1.ASPMX.L.GOOGLE.com (173.194.77.27)\n\t5 ALT2.ASPMX.L.GOOGLE.com (209.85.225.27)\n\t10 ASPMX2.GOOGLEMAIL.com (173.194.69.27)\n\t10 ASPMX3.GOOGLEMAIL.com (74.125.127.27)\n	c040a80e34a5029eaea6ff3eb2a6bd9f708e8ceeec11c10ff67df8202ab6e310	\N	2012-06-29 04:07:47.051267	25493	finished	5cedf7403986d2e7585cbd1acf7c71e5ee22845e0a886a21e780e98a3d9fc03e	3	\N	\N	\N
15	61	Possible Admin Access found here: http://192.168.1.1:80/   The response code was:401 Unauthorized\n	e2e7aae83b0f655aacc6cea9f773732aec51b9ad2891764e70825bbc30510213	\N	2012-06-28 00:41:22.088289	19659	finished	1cb6ac1b5e8799faf38c0ea569b205b8f452c2c962f5f6eb381f07effdbf20a8	3	http	80	192.168.1.1
15	1	Host not found.	dbb1dd953b35f99a946cc8cc33ec80ddcfa1d85afdd1038e3aafda5eb64c64ba	\N	2012-06-29 10:07:24.192001	27419	finished	051f14f7abadf20555ee3e282e20f118759aad4bee6c5bdbdf32faae790601d7	3	\N	\N	\N
17	41	tried 8 time(s) with 0 successful time(s)\n	2cac60a40c685f47928ee45a8e4405bbf1568f27a88bc21e255d2854e317a6c6	\N	2012-06-27 07:47:55.734753	13162	finished	c8ab9ca3164d5c6039ddf80a860ef291a0074382baf1c2b8e9f8efbbb5ec1691	3	\N	\N	\N
14	25	NoHostName: No host name specified.	64de16d595b904a27fb839227b9c447832da45a71c89a0aa1e1529ab6604bf79	\N	2012-06-27 06:37:54.162189	12338	finished	c1176e1df6e24e49027130b68155bed996a5e9dd384687dca2481df7bb158ce5	3	\N	\N	\N
14	62	No output.	525f57214b7c6572608128826a4ec451fab313bd30b710c3264f654c8ac8fe67	\N	2012-06-29 13:29:02.677598	\N	finished	1b1054b600dc572f200f5659368b0a98a4f0374ab9633cd69a1091eb4721ef6d	3	http	80	\N
3	33	No output.	55f2a4c16f8a20b4015f8d5916b125e9faaa7e4dd2aec3e67c9ec7fea6a6fb7f	\N	2012-06-29 22:19:24.549022	\N	finished	e92f92065c7108f625fa3e16c70c7037a6f70fccd5da380e9ae76bb99863763d	3	\N	\N	\N
14	33	DNS Servers for onexchanger.com:\n\tdns2.registrar-servers.com\n\tdns3.registrar-servers.com\n\tdns4.registrar-servers.com\n\tdns5.registrar-servers.com\n\tdns1.registrar-servers.com\n\tTesting dns2.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns3.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns4.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns5.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns1.registrar-servers.com\n\t\tRequest timed out or transfer not allowed.\n	a57f8a3bdcd085c614ec0e8f839b238a6cb0b40e0d3b8f96d73c2a222d747477	\N	2012-06-29 04:08:11.505631	25510	finished	53f720e21eb3dbad60abdbf5161593727b70f0e1da42c956c67d919cfd2baba5	3	\N	\N	\N
14	39	onexchanger.ws\t\t64.62.153.174\n	10c0320fef18c6fc88a83825251c234d364956e61b469e6bd11cf0d926f75140	\N	2012-06-27 06:58:07.033144	12531	finished	2ad9e6eead5a0fa0f71cb562c724d59325aa21a9ef7ce900e403a9a747eff82d	3	\N	\N	\N
14	37	46.4.98.106\n	a2c84a7190546227add63c5f0831ea4403fc4de27c8be07d8fe44daf2d81478f	\N	2012-06-27 06:34:40.681956	12248	finished	8feb115003f450319ac3781b8dc14842fa357c1186c01eb84e14c6530a5c95db	3	\N	\N	\N
14	38	SPF record: v=spf1 include:_spf.google.com ~all\n	584e358686813042a3283bc9c06221686baf3a1c08cb77d29bbfbc16da106cdf	\N	2012-06-27 06:36:46.611073	12279	finished	d8ad4b935f674bcd4ce3af889dd71894f99049e70065f1e2d5fd1dc4e75cefb1	3	\N	\N	\N
14	1	DNS request timeout.	8f26668fd65465738953179b3e548e5f30928faf6916b4177cd3001a1e5e6539	\N	2012-06-29 04:07:56.897508	25503	finished	cffb9563e9bca384c668dec3c86d15b6d63b615ff2cf38b8432f16f8f864d76d	3	\N	\N	yandex.ru
14	40	tried 9 user:pass combinations on retain.io, none succeeded...\n	da511247904a2446fdbc6f2165169189f6dab0f81395f4b4b5bb737fafcd2359	\N	2012-06-27 07:09:08.684808	12621	finished	338c229f8473d2cd24789fc076d17ba185993bd0f0da608bf4ee122b23d53de8	3	\N	\N	retain.io
17	42	www.gtta.org.uk\n	05149d8eaf654249a6f4ded3fbddcdaf18c7be3a857c5d97586283d2b5b83352	\N	2012-06-27 08:10:46.021949	14121	finished	ee1b7bdec0267de3718e97d4e943f5b4fc056b1133c3c0eb55d8144743d985ab	3	\N	\N	\N
15	47	aostrip.ru\nbostrip.ru\ncostrip.ru\ndostrip.ru\neostrip.ru\nfostrip.ru\ng-ostrip.ru\ng0strip.ru\ngastrip.ru\ngbstrip.ru\ngcstrip.ru\ngdstrip.ru\ngestrip.ru\ngfstrip.ru\nggostrip.ru\nggstrip.ru\nghstrip.ru\ngistrip.ru\ngjstrip.ru\ngkstrip.ru\nglstrip.ru\ngmstrip.ru\ngnstrip.ru\ngo-strip.ru\ngoatrip.ru\ngobtrip.ru\ngoctrip.ru\ngodtrip.ru\ngoetrip.ru\ngoftrip.ru\ngogtrip.ru\ngohtrip.ru\ngoitrip.ru\ngojtrip.ru\ngoktrip.ru\ngoltrip.ru\ngomtrip.ru\ngontrip.ru\ngoostrip.ru\ngootrip.ru\ngoptrip.ru\ngoqtrip.ru\ngortrip.ru\ngos-trip.ru\ngosarip.ru\ngosbrip.ru\ngoscrip.ru\ngosdrip.ru\ngoserip.ru\ngosfrip.ru\ngosgrip.ru\ngoshrip.ru\ngosirip.ru\ngosjrip.ru\ngoskrip.ru\ngoslrip.ru\ngosmrip.ru\ngosnrip.ru\ngosorip.ru\ngosprip.ru\ngosqrip.ru\ngosrip.ru\ngosrrip.ru\ngosrtip.ru\ngossrip.ru\ngosstrip.ru\ngost-rip.ru\ngostaip.ru\ngostbip.ru\ngostcip.ru\ngostdip.ru\ngosteip.ru\ngostfip.ru\ngostgip.ru\ngosthip.ru\ngostiip.ru\ngostip.ru\ngostirp.ru\ngostjip.ru\ngostkip.ru\ngostlip.ru\ngostmip.ru\ngostnip.ru\ngostoip.ru\ngostpip.ru\ngostqip.ru\ngostr-ip.ru\ngostrap.ru\ngostrbp.ru\ngostrcp.ru\ngostrdp.ru\ngostrep.ru\ngostrfp.ru\ngostrgp.ru\ngostrhp.ru\ngostri.ru\ngostria.ru\ngostrib.ru\ngostric.ru\ngostrid.ru\ngostrie.ru\ngostrif.ru\ngostrig.ru\ngostrih.ru\ngostrii.ru\ngostriip.ru\ngostrij.ru\ngostrik.ru\ngostril.ru\ngostrim.ru\ngostrin.ru\ngostrio.ru\ngostrip.ru\ngostripp.ru\ngostriq.ru\ngostrir.ru\ngostris.ru\ngostrit.ru\ngostriu.ru\ngostriv.ru\ngostriw.ru\ngostrix.ru\ngostriy.ru\ngostriz.ru\ngostrjp.ru\ngostrkp.ru\ngostrlp.ru\ngostrmp.ru\ngostrnp.ru\ngostrop.ru\ngostrp.ru\ngostrpi.ru\ngostrpp.ru\ngostrqp.ru\ngostrrip.ru\ngostrrp.ru\ngostrsp.ru\ngostrtp.ru\ngostrup.ru\ngostrvp.ru\ngostrwp.ru\ngostrxp.ru\ngostryp.ru\ngostrzp.ru\ngostsip.ru\ngosttip.ru\ngosttrip.ru\ngostuip.ru\ngostvip.ru\ngostwip.ru\ngostxip.ru\ngostyip.ru\ngostzip.ru\ngosurip.ru\ngosvrip.ru\ngoswrip.ru\ngosxrip.ru\ngosyrip.ru\ngoszrip.ru\ngotrip.ru\ngotsrip.ru\ngottrip.ru\ngoutrip.ru\ngovtrip.ru\ngowtrip.ru\ngoxtrip.ru\ngoytrip.ru\ngoztrip.ru\ngpstrip.ru\ngqstrip.ru\ngrstrip.ru\ngsotrip.ru\ngsstrip.ru\ngstrip.ru\ngtstrip.ru\ngustrip.ru\ngvstrip.ru\ngwstrip.ru\ngxstrip.ru\ngystrip.ru\ngzstrip.ru\nhostrip.ru\niostrip.ru\njostrip.ru\nkostrip.ru\nlostrip.ru\nmostrip.ru\nnostrip.ru\nogstrip.ru\noostrip.ru\nostrip.ru\npostrip.ru\nqostrip.ru\nrostrip.ru\nsostrip.ru\ntostrip.ru\nuostrip.ru\nvostrip.ru\nwostrip.ru\nxostrip.ru\nyostrip.ru\nzostrip.ru\n	ff9dad93ada0f3fb1b4ef73bd76f22901bcebfe6c7748c9054098c5b52aaf134	\N	2012-06-29 10:07:23.964928	27425	finished	1295e1930e601f0ad0e9ab4684aab17b20346a495f7d79d960d243f0f1d141c4	3	\N	\N	\N
15	62	Possible Directory found here: http://retain.io:80/images/   The response code was:403 Forbidden\nPossible Directory found here: http://retain.io:80/css/   The response code was:403 Forbidden\n	7907fb1caf1c17ba9d8494d602d76d8c080e00520ec2fa748bf52d7035f6626b	\N	2012-06-28 00:48:25.707053	19790	finished	a7c2681c0cf575281e1d499ac9f533301b57bfbe5f0375a3d92408c6511c036f	3	http	80	retain.io
14	35	\n1) onexchanger.com (46.4.98.106)\n\tdns5.registrar-servers.com (95.211.9.35)\n\tdns1.registrar-servers.com (38.101.213.194)\n\tdns2.registrar-servers.com (208.64.122.242)\n\tdns3.registrar-servers.com (67.228.228.216)\n\tdns4.registrar-servers.com (184.173.147.66)\n	08a06ddc4ed3c907e861196235efedd6ae7a5fb45bffc4966ebc26b7ee735436	\N	2012-06-29 04:07:47.074561	25498	finished	831a7bb019172ca2a5ccd644d397e97b80e5f8cad6eecb66b6ee392900d8c648	3	\N	\N	\N
14	36	46.4.98.106\t\tstatic.106.98.4.46.clients.your-server.de.\n	7f10f9ed7fb7c7835e5023e783b14fa2e9b3804265d95087e3fedf5291286ead	\N	2012-06-29 04:07:47.083816	25500	finished	3ffcfc5cec3e22726bb36459de7b8e0b5fbb6744361ba58b59980afad10ffed0	3	\N	\N	46.4.98.106
15	63	Possible File found here: http://gostrip.ru:80/index.php  \t SIZE 15857 \t CODE:200 OK\n	b43917f2dfb5813e03815b51ef2c5019bb1b405146db3019ac701cc4171c4219	\N	2012-06-28 00:46:33.537843	19728	finished	c37052dc5455d1affcbd500f58c3fcf58e31b39fa73b9bfb6cda8c3b8a052294	3	http	80	\N
17	44	501 Protocol scheme 'https' is not supported (Crypt::SSLeay or IO::Socket::SSL not installed)\nContent-Type: text/plain\nClient-Date: Wed, 27 Jun 2012 12:34:37 GMT\nClient-Warning: Internal response\n\nLWP will support https URLs if either Crypt::SSLeay or IO::Socket::SSL\nis installed. More information at\n<http://search.cpan.org/dist/libwww-perl/README.SSL>.\\n\n\n	fcf983734cc5b4b21dc866e47645b65e97ea3a0b66fa50d219fee0ba9bdf8918	\N	2012-06-27 16:34:37.862412	15086	finished	df5a76155c4b533e9ea9130acb8aec71f89a55d7387c5fc167c9b70e07030fae	3	https	\N	\N
17	45	Searching for firewall\nSearching for components \nSearching for copyright of version 1.0\nSearching for copyright of version 1.5\nScanning files.txt \nsearch for unprotected files(generic.txt)\n\nsearch for possibles bugs(Core)\n\nsearch for possibles bugs(Component)\n\nScan finished\nRunning on Apache/2.2.16 (Debian)\n\nNo firewall detected\n\n\nComponents:\n\nVersion unknown\n\nSecurity tips:\n=============\nPossible vulnerabilities in core:\n================================\n\nPossible vulnerabilities in components:\n======================================\n\n	1d5031b521c480327d5d4f9822493009a240ea0f4170772a4144838f96656b6b	\N	2012-06-27 17:07:54.215252	15235	finished	e67d1a864d6d367fb9798512d534407bcf5e7387799d5d8808b33b27b3bdc977	3	\N	\N	\N
14	48	report for onexchanger.com (46.4.98.106)\n% This is the RIPE Database query service.\n% The objects are in RPSL format.\n%\n% The RIPE Database is subject to Terms and Conditions.\n% See http://www.ripe.net/db/support/db-terms-conditions.pdf\n\n% Note: this output has been filtered.\n%       To receive output for a database update, use the "-B" flag.\n\n% Information related to '46.4.98.96 - 46.4.98.127'\n\ninetnum:         46.4.98.96 - 46.4.98.127\nnetname:         HETZNER-RZ14\ndescr:           Hetzner Online AG\ndescr:           Datacenter 14\ncountry:         DE\nadmin-c:         HOAC1-RIPE\ntech-c:          HOAC1-RIPE\nstatus:          ASSIGNED PA\nmnt-by:          HOS-GUN\nmnt-lower:       HOS-GUN\nmnt-routes:      HOS-GUN\nsource:          RIPE # Filtered\n\n% Information related to '46.4.0.0/16AS24940'\n\nroute:          46.4.0.0/16	2a328e6ef5340479089cb44cae4429fbb50740be28c9646ac0f379e72f6bcd90	\N	2012-06-29 04:07:48.338374	25508	finished	aa5a313ab188e54b201d0b09018d2b5a9c3c6d3a0a92a3c5baf11d9c71e315fc	3	\N	\N	\N
3	35	\n1) Error: no NS for domain dns1.registrar-servers.com (NOERROR)\n	4a21814d345d743abc24eff274a90d69da4ece8260f0008a501917e1ddc76021	\N	2012-06-29 22:19:24.554914	\N	finished	88720d778c11299b67fe76f5bc69d0748dec3838aff100c99620743d566672de	3	\N	\N	\N
14	42	No output.	035a881fbf3dbca9575cfbea653ef60d220333b7af3a68891eac2d196777a3f1	\N	2012-06-29 13:29:03.77762	\N	finished	dcce41b736275a030c172f7b1b371ded016c4ac1e2692eb7dd723e7d0cf91081	3	\N	\N	\N
17	46	\n\n->[+] Target : http://gtta.demonstratr.com/\n->[+] Basic c0de of the site : php\n->[+] Scanning control panel page...\n\n\n\n[+] http://gtta.demonstratr.com/administrator.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administrator/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/moderator/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webadmin/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminarea/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/bb-admin/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminLogin/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin_area/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/panel-administracion/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/instadmin/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/memberadmin/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administratorlogin/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adm/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/account.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/index.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/admin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/account.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin_area/admin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin_area/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/siteadmin/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/siteadmin/index.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/siteadmin/login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/account.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/index.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/admin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin_area/index.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/bb-admin/index.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/bb-admin/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/bb-admin/admin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/home.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin_area/login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin_area/index.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/controlpanel.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admincp/index.asp \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admincp/login.asp \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admincp/index.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/account.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminpanel.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webadmin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webadmin/index.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webadmin/admin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webadmin/login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/admin_login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin_login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/panel-administracion/login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/cp.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/cp.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administrator/index.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administrator/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/nsw/admin/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webadmin/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/admin_login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin_login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administrator/account.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administrator.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin_area/admin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/pages/admin/admin-login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/admin-login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin-login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/bb-admin/index.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/bb-admin/login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/bb-admin/admin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/home.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/modelsearch/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/moderator.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/moderator/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/moderator/admin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/account.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/pages/admin/admin-login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/admin-login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin-login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/controlpanel.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admincontrol.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/adminLogin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminLogin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/adminLogin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/home.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/rcjakar/admin/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminarea/index.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminarea/admin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webadmin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webadmin/index.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webadmin/admin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/controlpanel.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/cp.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/cp.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminpanel.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/moderator.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administrator/index.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administrator/login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/user.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administrator/account.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administrator.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/modelsearch/login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/moderator/login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminarea/login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/panel-administracion/index.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/panel-administracion/admin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/modelsearch/index.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/modelsearch/admin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admincontrol/login.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adm/index.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adm.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/moderator/admin.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/user.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/account.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/controlpanel.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admincontrol.html \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/panel-administracion/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/wp-login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminLogin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin/adminLogin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/home.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/secureadmin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminarea/index.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminarea/admin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminarea/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/panel-administracion/index.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/panel-administracion/admin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/modelsearch/index.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/modelsearch/admin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admincontrol/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adm/admloginuser.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admloginuser.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin2.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin2/login.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin2/index.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adm/index.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adm.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/affiliate.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adm_auth.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/memberadmin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administratorlogin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/secureadmin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/secureadmin/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/verysecure.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/securelogon.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin2009.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webadministration/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webadministrasi.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admininput.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/secure.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/secureadministration.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/phpmyadmin/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/sosecure.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/hardfound.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/dificultadmin.php/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administracion/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/root.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/locked.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/locked/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminnn.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminsitus.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminsitus/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminsite/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminsite.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administratorsite/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminpageonly/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminonly.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin-site.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/admin-site/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/administratorsite.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/usersite.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/maintenance.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/reconstruct.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/pageadmin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/usersdatabase.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/databaseuser.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/databaseusers/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webdatalogin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/dataadministration.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/homeadmin/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/fjk.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/database.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/database/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/dataweb/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/qwerty.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/account.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/account.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/testaccount.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/accountlogon.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/account2009/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/accountlogin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webaccount.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/databaseuserlogin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/databaseadministration/ \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/database.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/loggon.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/myadmin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webadmin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/checkadmin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/homeweb.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/webhome.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/adminarea.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/logonpanel.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n\n[+] http://gtta.demonstratr.com/loginwebadmin.php \n[!] status => 404 CHttpException\n[!] Admin page Login Possibilities => NO...\n\n	43af24dd0d05326c73b3d502c6c86e7b3429365fbbdbd253d7a07be52bb58a3c	\N	2012-06-27 17:18:33.937509	15314	finished	e0c02768cf80911b3ad8e3b6c1750eba36ec2eeb6df4b565001d3ae5a406a366	3	\N	\N	\N
15	33	DNS Servers for gostrip.ru:\n\tdns2.webdrive.ru\n\tdns1.webdrive.ru\n\tTesting dns2.webdrive.ru\n\t\tRequest timed out or transfer not allowed.\n\tTesting dns1.webdrive.ru\n\t\tRequest timed out or transfer not allowed.\n	9d567d066c0c635351dfb9eafcb36387dfedd6f95e11745c92edcbf41309a75b	\N	2012-06-29 10:07:24.526061	27421	finished	85d255941bac97739d443c8cc0b2dc24b967e66a06822d4f8b09661a6ca825a4	3	\N	\N	\N
17	47	googpe.com\t\t\t208.87.34.15\ngoogpe.com\t\t\t23.23.210.22\ngooglz.com\t\t\t202.31.187.154\ngoxgle.com\t\t\t98.124.198.1\nkoogle.com\t\t\t74.86.197.160\nkoogle.com\t\t\t208.87.34.15\ngrogle.com\t\t\t141.8.224.106\nooogle.com\t\t\t74.86.197.160\nooogle.com\t\t\t208.87.34.15\ngoole.com\t\t\t213.165.70.39\ngowgle.com\t\t\t64.202.189.170\ngoonle.com\t\t\t141.8.224.106\ngoogl.com\t\t\t173.194.35.244\ngoogl.com\t\t\t173.194.35.240\ngoogl.com\t\t\t173.194.35.241\ngoogl.com\t\t\t173.194.35.242\ngoogl.com\t\t\t173.194.35.243\nvoogle.com\t\t\t85.17.35.48\nvoogle.com\t\t\t85.17.35.51\ngqogle.com\t\t\t199.59.241.215\ngoogfe.com\t\t\t23.23.210.22\ngoogfe.com\t\t\t208.87.34.15\ngoosle.com\t\t\t208.87.34.15\ngoosle.com\t\t\t74.86.197.160\ngolgle.com\t\t\t69.43.160.153\ngooglt.com\t\t\t64.202.189.170\ngooglq.com\t\t\t199.59.241.215\nogogle.com\t\t\t74.125.232.83\nogogle.com\t\t\t74.125.232.84\nogogle.com\t\t\t74.125.232.80\nogogle.com\t\t\t74.125.232.81\nogogle.com\t\t\t74.125.232.82\ngoogme.com\t\t\t199.59.241.215\ngoogje.com\t\t\t69.43.160.189\ngoogoe.com\t\t\t63.156.206.48\ncoogle.com\t\t\t82.98.86.161\ngoagle.com\t\t\t89.145.66.234\ngoogxe.com\t\t\t216.8.179.25\ngoogel.com\t\t\t173.194.35.242\ngoogel.com\t\t\t173.194.35.243\ngoogel.com\t\t\t173.194.35.244\ngoogel.com\t\t\t173.194.35.240\ngoogel.com\t\t\t173.194.35.241\naoogle.com\t\t\t204.13.162.123\ngoorle.com\t\t\t64.15.205.100\ngoorle.com\t\t\t64.15.205.101\ngoorle.com\t\t\t208.48.81.133\ngoorle.com\t\t\t208.48.81.134\nioogle.com\t\t\t202.191.42.62\ngooogle.com\t\t\t173.194.35.240\ngooogle.com\t\t\t173.194.35.241\ngooogle.com\t\t\t173.194.35.242\ngooogle.com\t\t\t173.194.35.243\ngooogle.com\t\t\t173.194.35.244\ngoogue.com\t\t\t69.43.161.205\ngopgle.com\t\t\t208.87.34.15\ngopgle.com\t\t\t74.86.197.160\ngoovle.com\t\t\t89.149.223.128\ngdogle.com\t\t\t82.98.86.176\ndoogle.com\t\t\t195.238.172.28\ngooglj.com\t\t\t50.63.50.83\ngooghe.com\t\t\t199.59.241.215\ngogle.com\t\t\t173.194.35.241\ngogle.com\t\t\t173.194.35.242\ngogle.com\t\t\t173.194.35.243\ngogle.com\t\t\t173.194.35.244\ngogle.com\t\t\t173.194.35.240\ngoygle.com\t\t\t69.43.160.208\ngooale.com\t\t\t204.236.217.167\ngooale.com\t\t\t216.67.232.70\ngiogle.com\t\t\t216.151.212.175\ngocgle.com\t\t\t199.59.241.215\ngoogze.com\t\t\t69.73.169.188\ngogole.com\t\t\t173.194.35.240\ngogole.com\t\t\t173.194.35.241\ngogole.com\t\t\t173.194.35.242\ngogole.com\t\t\t173.194.35.243\ngogole.com\t\t\t173.194.35.244\ngoggle.com\t\t\t85.17.162.41\ngoggle.com\t\t\t85.17.162.24\ngo-ogle.com\t\t\t69.25.27.173\ngo-ogle.com\t\t\t207.228.235.44\ngo-ogle.com\t\t\t63.251.171.80\ngo-ogle.com\t\t\t63.251.171.81\ngo-ogle.com\t\t\t66.150.161.140\ngo-ogle.com\t\t\t66.150.161.141\ngo-ogle.com\t\t\t69.25.27.170\ngoogee.com\t\t\t199.188.207.19\nyoogle.com\t\t\t127.0.0.1\neoogle.com\t\t\t204.13.162.123\ngoogly.com\t\t\t82.98.86.169\ngfogle.com\t\t\t174.37.175.243\ngaogle.com\t\t\t208.109.181.212\nggoogle.com\t\t\t74.125.232.84\nggoogle.com\t\t\t74.125.232.80\nggoogle.com\t\t\t74.125.232.81\nggoogle.com\t\t\t74.125.232.82\nggoogle.com\t\t\t74.125.232.83\ngoogwe.com\t\t\t74.53.172.34\ngooglh.com\t\t\t208.48.81.134\ngooglh.com\t\t\t64.15.205.100\ngooglh.com\t\t\t64.15.205.101\ngooglh.com\t\t\t208.48.81.133\ngtogle.com\t\t\t141.8.224.141\ngoojle.com\t\t\t208.87.34.15\ngoojle.com\t\t\t74.86.197.160\nglogle.com\t\t\t63.156.206.48\ngoolge.com\t\t\t173.194.35.243\ngoolge.com\t\t\t173.194.35.244\ngoolge.com\t\t\t173.194.35.240\ngoolge.com\t\t\t173.194.35.241\ngoolge.com\t\t\t173.194.35.242\ngoogls.com\t\t\t208.87.34.15\ngoogls.com\t\t\t74.86.197.160\ngohgle.com\t\t\t69.43.161.211\nuoogle.com\t\t\t64.202.189.170\nghogle.com\t\t\t62.116.181.25\ngofgle.com\t\t\t204.13.162.123\nsoogle.com\t\t\t112.65.128.54\ngoople.com\t\t\t212.110.186.244\ngoogln.com\t\t\t199.59.241.215\ngooglee.com\t\t\t173.194.35.241\ngooglee.com\t\t\t173.194.35.242\ngooglee.com\t\t\t173.194.35.243\ngooglee.com\t\t\t173.194.35.244\ngooglee.com\t\t\t173.194.35.240\ngoqgle.com\t\t\t68.178.232.100\ngbogle.com\t\t\t66.246.235.44\ngovgle.com\t\t\t72.167.232.54\nnoogle.com\t\t\t72.51.27.51\ngozgle.com\t\t\t82.98.86.169\ngyogle.com\t\t\t68.178.232.100\ngodgle.com\t\t\t74.86.197.160\ngodgle.com\t\t\t208.87.34.15\ngooglr.com\t\t\t173.194.35.240\ngooglr.com\t\t\t173.194.35.241\ngooglr.com\t\t\t173.194.35.242\ngooglr.com\t\t\t173.194.35.243\ngooglr.com\t\t\t173.194.35.244\ngoozle.com\t\t\t72.167.131.153\ngooge.com\t\t\t97.74.27.1\nwoogle.com\t\t\t216.65.41.188\nguogle.com\t\t\t82.98.86.175\ngoogte.com\t\t\t216.151.212.175\n	b18d60fb3aaaabf23ae0cb0dfca80117990e3ce64d78e47e50c78d42f10707cc	\N	2012-06-27 17:48:17.543295	15565	finished	9980b55737f17acd52826e1f7c8ba781ef40df5c03370f061cc17302b5c288e7	3	\N	\N	google.com
14	54	[root:123]\n[root:456]\n[root:f/xDQaTpzZAPx?C/@wY9NH4U9]\n[john:123]\n[john:456]\n[john:f/xDQaTpzZAPx?C/@wY9NH4U9]\n[anton:123]\n[anton:456]\n[anton:f/xDQaTpzZAPx?C/@wY9NH4U9]\ntried 9 user:pass combinations on onexchanger.com, none succeeded...\n	b5d0699d0c2409d0683b250c075c94d98c65ac73b4cf77a6e4f9bd1989b459ae	\N	2012-06-27 21:57:10.231954	17919	finished	3dca418688a3e061aa8038ba3924b55f05bd33ac1817cc316f3a3b70b6e55184	3	\N	\N	\N
15	56	FOUND: http://gostrip.ru/admin/\nFOUND: http://gostrip.ru/index.php\n	02a5df0cb433a313b368cc4e78ad2e28a44da202f8ff01c75f2d519f4053b97f	\N	2012-06-27 23:50:54.201115	18653	finished	fc903ce983596db677951fa0487908abe1e639fc93fd3c6007222127d211bf22	3	http	\N	\N
17	48	report for gtta.demonstratr.com (78.46.202.166)\n% This is the RIPE Database query service.\n% The objects are in RPSL format.\n%\n% The RIPE Database is subject to Terms and Conditions.\n% See http://www.ripe.net/db/support/db-terms-conditions.pdf\n\n% Note: this output has been filtered.\n%       To receive output for a database update, use the "-B" flag.\n\n% Information related to '78.46.202.160 - 78.46.202.175'\n\ninetnum:         78.46.202.160 - 78.46.202.175\nnetname:         HETZNER-ONLINE-AG-VIRTUALISIERUNG-POOL15\ndescr:           Hetzner Online AG - Virtualisierung\ncountry:         DE\nadmin-c:         HOAV1-RIPE\ntech-c:          HOAV1-RIPE\nstatus:          ASSIGNED PA\nmnt-by:          HOS-GUN\nsource:          RIPE # Filtered\n\n% Information related to '78.46.0.0/15AS24940'\n\nroute:          78.46.0.0/15	6182f15cd492865101bcb854c887792365755191a2cf1aedbc996ce1ced91c65	\N	2012-06-27 18:11:52.028475	15861	finished	89615b62c9da56a422c7c3da0f7900d34d984ffc09a3f23dc37b0bbd04fa7980	3	\N	\N	\N
3	50	;; connection timed out; no servers could be reached	4485ba8662f175b2d0d085f2037b7e37ee1fda3665a306a74e92fdda4b66b7b3	\N	2012-06-29 22:19:38.808053	\N	finished	3c5176c8ca160207f3089021b9aadfb40d6bfacd0d3083691e1aca511225109a	3	\N	\N	dns2.registrar-servers.com
3	47	ans1.registrar-servers\nbns1.registrar-servers\ncns1.registrar-servers\nd-ns1.registrar-servers\ndas1.registrar-servers\ndbs1.registrar-servers\ndcs1.registrar-servers\nddns1.registrar-servers\ndds1.registrar-servers\ndes1.registrar-servers\ndfs1.registrar-servers\ndgs1.registrar-servers\ndhs1.registrar-servers\ndis1.registrar-servers\ndjs1.registrar-servers\ndks1.registrar-servers\ndls1.registrar-servers\ndms1.registrar-servers\ndn-s1.registrar-servers\ndn1.registrar-servers\ndn1s.registrar-servers\ndna1.registrar-servers\ndnb1.registrar-servers\ndnc1.registrar-servers\ndnd1.registrar-servers\ndne1.registrar-servers\ndnf1.registrar-servers\ndng1.registrar-servers\ndnh1.registrar-servers\ndni1.registrar-servers\ndnj1.registrar-servers\ndnk1.registrar-servers\ndnl1.registrar-servers\ndnm1.registrar-servers\ndnn1.registrar-servers\ndnns1.registrar-servers\ndno1.registrar-servers\ndnp1.registrar-servers\ndnq1.registrar-servers\ndnr1.registrar-servers\ndns.registrar-servers\ndns1.registrar-servers\ndns11.registrar-servers\ndnsa.registrar-servers\ndnsb.registrar-servers\ndnsc.registrar-servers\ndnsd.registrar-servers\ndnse.registrar-servers\ndnsf.registrar-servers\ndnsg.registrar-servers\ndnsh.registrar-servers\ndnsi.registrar-servers\ndnsj.registrar-servers\ndnsk.registrar-servers\ndnsl.registrar-servers\ndnsm.registrar-servers\ndnsn.registrar-servers\ndnso.registrar-servers\ndnsp.registrar-servers\ndnsq.registrar-servers\ndnsr.registrar-servers\ndnss.registrar-servers\ndnss1.registrar-servers\ndnst.registrar-servers\ndnsu.registrar-servers\ndnsv.registrar-servers\ndnsw.registrar-servers\ndnsx.registrar-servers\ndnsy.registrar-servers\ndnsz.registrar-servers\ndnt1.registrar-servers\ndnu1.registrar-servers\ndnv1.registrar-servers\ndnw1.registrar-servers\ndnx1.registrar-servers\ndny1.registrar-servers\ndnz1.registrar-servers\ndos1.registrar-servers\ndps1.registrar-servers\ndqs1.registrar-servers\ndrs1.registrar-servers\nds1.registrar-servers\ndsn1.registrar-servers\ndss1.registrar-servers\ndts1.registrar-servers\ndus1.registrar-servers\ndvs1.registrar-servers\ndws1.registrar-servers\ndxs1.registrar-servers\ndys1.registrar-servers\ndzs1.registrar-servers\nens1.registrar-servers\nfns1.registrar-servers\ngns1.registrar-servers\nhns1.registrar-servers\nins1.registrar-servers\njns1.registrar-servers\nkns1.registrar-servers\nlns1.registrar-servers\nmns1.registrar-servers\nnds1.registrar-servers\nnns1.registrar-servers\nns1.registrar-servers\nons1.registrar-servers\npns1.registrar-servers\nqns1.registrar-servers\nrns1.registrar-servers\nsns1.registrar-servers\ntns1.registrar-servers\nuns1.registrar-servers\nvns1.registrar-servers\nwns1.registrar-servers\nxns1.registrar-servers\nyns1.registrar-servers\nzns1.registrar-servers\n	e79f26bb6366311328db1e5a22a9b2607e8187d74697eebf005b4cba85a759bc	\N	2012-06-29 22:19:24.405822	\N	finished	90682d217b73f847d9df0318a12398384bf0905b28e84a8e6564c3c4cb744e66	3	\N	\N	\N
14	56	FOUND: http://lenta.ru/rss/\nFOUND: http://lenta.ru/rss\n	c0ef0ae8bbc86551f3ee8c6613b5626bd9ff83552ab6964ef3cbee3082d8485a	\N	2012-06-29 13:29:03.398607	\N	finished	99176ef0eb502c595397df7f97d2f62c86214f12d27252f7d538bd6cfec51302	3	http	\N	lenta.ru
17	49	- Nikto v2.1.4\n---------------------------------------------------------------------------\n+ Target IP:          78.46.202.166\n+ Target Hostname:    gtta.demonstratr.com\n+ Target Port:        80\n+ Start Time:         2012-06-28 19:47:27\n---------------------------------------------------------------------------\n+ Server: Apache/2.2.16 (Debian)\n+ Retrieved x-powered-by header: PHP/5.3.3-7+squeeze9\n+ Root page / redirects to: http://gtta.demonstratr.com/login\n+ No CGI Directories found (use '-C all' to force check all possible dirs)\n+ Apache/2.2.16 appears to be outdated (current is at least Apache/2.2.17). Apache 1.3.42 (final release) and 2.0.64 are also current.\n+ OSVDB-3268: http://127.0.0.1:2301/ HTTP/1.0: Directory indexing found.\n+ OSVDB-12184: /index.php?=PHPB8B5F2A0-3C92-11d3-A3A9-4C7B08C10000: PHP reveals potentially sensitive information via certain HTTP requests that contain specific QUERY strings.\n+ OSVDB-12184: /some.php?=PHPE9568F36-D428-11d2-A769-00AA001ACF42: PHP reveals potentially sensitive information via certain HTTP requests that contain specific QUERY strings.\n+ OSVDB-12184: /some.php?=PHPE9568F34-D428-11d2-A769-00AA001ACF42: PHP reveals potentially sensitive information via certain HTTP requests that contain specific QUERY strings.\n+ OSVDB-12184: /some.php?=PHPE9568F35-D428-11d2-A769-00AA001ACF42: PHP reveals potentially sensitive information via certain HTTP requests that contain specific QUERY strings.\n+ OSVDB-3092: /login/: This might be interesting...\n+ OSVDB-3268: /icons/: Directory indexing found.\n+ OSVDB-3268: /images/: Directory indexing found.\n+ OSVDB-3268: /images/?pattern=/etc/*&sort=name: Directory indexing found.\n+ OSVDB-3233: /icons/README: Apache default file found.\n+ 6448 items checked: 0 error(s) and 12 item(s) reported on remote host\n+ End Time:           2012-06-28 20:20:23 (1976 seconds)\n---------------------------------------------------------------------------\n+ 1 host(s) tested\n	c094bd8a21a3c261d06c74f39f5fdbbb6fd8f99b33a25d684e79052dd4bb7aed	\N	2012-06-27 20:20:23.462083	16748	finished	02d8f8d7ddbde5a13db116beb1cec80ea8abfbc630ed9dba8b31ab0e57957d7b	3	http	80	\N
14	55	DNS Servers for onexchanger.com:\n\tdns5.registrar-servers.com\n\tdns1.registrar-servers.com\n\tdns2.registrar-servers.com\n\tdns3.registrar-servers.com\n\tdns4.registrar-servers.com\n\nChecking for wildcard DNS...\nNope. Good.\nNow performing 17 test(s)...\n46.4.98.106\twww.onexchanger.com\n\nSubnets found:\n\t46.4.98.0-255 : 1 hostname(s) found.\n	ff20b2f1c247dc6af8676b5943fd8c59ce07efb63b596718f68d36024c86c061	\N	2012-06-27 22:34:13.00403	18223	finished	1876511fad6d8d0084a501c8a32573ec1c83b9f86596a177447ecc9b73d2a3b6	3	\N	\N	\N
14	53	OK: server smtp.gmail.com connect successful\n	71e414f5fa04949464536850fd9ceade32c6decae69880a043066621f60ff6fe	\N	2012-06-27 21:49:28.228799	17690	finished	a9f7793b6f22888ddcb092e9a382dfcdf7d1f1753f0028b38eb65465016184b2	3	\N	\N	smtp.gmail.com
14	51	Port 1 ( 1 ) is closed\nPort 2 ( 2 ) is closed\nPort 3 ( 3 ) is closed\nPort 4 ( 4 ) is closed\nPort 5 ( 5 ) is closed\nPort 6 ( 6 ) is closed\nPort 7 ( 7 ) is closed\nPort 8 ( 8 ) is closed\nPort 9 ( 9 ) is closed\nPort 10 ( 10 ) is closed\nPort 11 ( 11 ) is closed\nPort 12 ( 12 ) is closed\nPort 13 ( 13 ) is closed\nPort 14 ( 14 ) is closed\nPort 15 ( 15 ) is closed\nPort 16 ( 16 ) is closed\nPort 17 ( 17 ) is closed\nPort 18 ( 18 ) is closed\nPort 19 ( 19 ) is closed\nPort 20 ( 20 ) is closed\nPort 21 ( 21 ) is closed\n\n\t\tPort 22 ( 22 ) is OPEN <<!!!\n\nPort 23 ( 23 ) is closed\nPort 24 ( 24 ) is closed\nPort 25 ( 25 ) is closed\nPort 26 ( 26 ) is closed\nPort 27 ( 27 ) is closed\nPort 28 ( 28 ) is closed\nPort 29 ( 29 ) is closed\nPort 30 ( 30 ) is closed\nPort 31 ( 31 ) is closed\nPort 32 ( 32 ) is closed\nPort 33 ( 33 ) is closed\nPort 34 ( 34 ) is closed\nPort 35 ( 35 ) is closed\nPort 36 ( 36 ) is closed\nPort 37 ( 37 ) is closed\nPort 38 ( 38 ) is closed\nPort 39 ( 39 ) is closed\nPort 40 ( 40 ) is closed\nPort 41 ( 41 ) is closed\nPort 42 ( 42 ) is closed\nPort 43 ( 43 ) is closed\nPort 44 ( 44 ) is closed\nPort 45 ( 45 ) is closed\nPort 46 ( 46 ) is closed\nPort 47 ( 47 ) is closed\nPort 48 ( 48 ) is closed\nPort 49 ( 49 ) is closed\nPort 50 ( 50 ) is closed\nPort 51 ( 51 ) is closed\nPort 52 ( 52 ) is closed\nPort 53 ( 53 ) is closed\nPort 54 ( 54 ) is closed\nPort 55 ( 55 ) is closed\nPort 56 ( 56 ) is closed\nPort 57 ( 57 ) is closed\nPort 58 ( 58 ) is closed\nPort 59 ( 59 ) is closed\nPort 60 ( 60 ) is closed\nPort 61 ( 61 ) is closed\nPort 62 ( 62 ) is closed\nPort 63 ( 63 ) is closed\nPort 64 ( 64 ) is closed\nPort 65 ( 65 ) is closed\nPort 66 ( 66 ) is closed\nPort 67 ( 67 ) is closed\nPort 68 ( 68 ) is closed\nPort 69 ( 69 ) is closed\nPort 70 ( 70 ) is closed\nPort 71 ( 71 ) is closed\nPort 72 ( 72 ) is closed\nPort 73 ( 73 ) is closed\nPort 74 ( 74 ) is closed\nPort 75 ( 75 ) is closed\nPort 76 ( 76 ) is closed\nPort 77 ( 77 ) is closed\nPort 78 ( 78 ) is closed\nPort 79 ( 79 ) is closed\n\n\t\tPort 80 ( 80 ) is OPEN <<!!!\n\nDone.\n	a7779a3842650fa2dd51227759224af905192aa01891adf757ad86361eb367d0	\N	2012-06-27 21:06:47.082351	17235	finished	db8093dbf0866e281c114df3fee4eb84bc7e0d72aa71531aae6a0ebd242a2197	3	\N	\N	\N
15	57	**** running cms plugin detection scanner ****\n+ CMS Plugins takes awhile....\n**** running Web Service scanner ****\n+ Web service Found: site uses google analytics\n	7cde2e4375f59867fc911e25138d6b6e772c72b2271a6c23f841a8499b04e80e	\N	2012-06-27 23:59:46.830619	18712	finished	d66c9f6ae293167a7b61bad5b8c17cdc8c7775f04c00b3487a7471803b80d769	3	\N	\N	\N
15	59	Killed	7d55109419596bf40180c0f43ebe5469bdd363c59682f98c8946c4780126274a	\N	2012-06-28 00:54:28.33966	19825	finished	37f0c89458229a287eaa7bf2c25222135ebd1e48260084dfe959e1983e898246	3	\N	\N	\N
3	37	66.90.82.194\n68.233.250.45\n69.16.244.25\n38.101.213.194\n50.7.230.26\n	bf359308c6a9c4bb87591f19d97d4b8f53a062bd96d2e0a7050180bd836cc727	\N	2012-06-29 22:19:24.391107	\N	finished	bf2674c8e13c324aa292c37de303582223b57ee96359365bfe666473936e318a	3	\N	\N	\N
14	31	No vulnerabilities detected.\nScanned 1 URLs.\n0.00 seconds elapsed.	6a125f5d5e23fd0d3a0d6260bda17d247c11f1ac450eba256c3dd4052a5a285c	\N	2012-06-29 13:28:58.719138	\N	finished	1e08e2ddf60e95252c1d42dfc1b75bd229ceb49673836b22e3d0ca525f97544c	3	\N	\N	\N
\.


--
-- TOC entry 2023 (class 0 OID 16546)
-- Dependencies: 168
-- Data for Name: targets; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY targets (id, project_id, host) FROM stdin;
12	8	127.0.0.1
16	12	dns1.registrar-servers.com
17	1	gtta.demonstratr.com
15	1	gostrip.ru
14	1	onexchanger.com
3	1	dns1.registrar-servers.com
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
-- TOC entry 1984 (class 2606 OID 16820)
-- Dependencies: 1925 140 152
-- Name: checks_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1985 (class 2606 OID 16825)
-- Dependencies: 152 1941 154
-- Name: checks_l10n_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1986 (class 2606 OID 16830)
-- Dependencies: 154 1951 157
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
-- Dependencies: 1945 161 155
-- Name: projects_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1989 (class 2606 OID 16702)
-- Dependencies: 163 152 1941
-- Name: target_check_attachments_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1990 (class 2606 OID 16707)
-- Dependencies: 163 168 1967
-- Name: target_check_attachments_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1991 (class 2606 OID 16712)
-- Dependencies: 164 1925 140
-- Name: target_check_categories_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1992 (class 2606 OID 16717)
-- Dependencies: 164 1967 168
-- Name: target_check_categories_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1993 (class 2606 OID 16722)
-- Dependencies: 165 152 1941
-- Name: target_check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id);


--
-- TOC entry 1994 (class 2606 OID 16727)
-- Dependencies: 1929 165 143
-- Name: target_check_inputs_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1995 (class 2606 OID 16732)
-- Dependencies: 1967 165 168
-- Name: target_check_inputs_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1996 (class 2606 OID 16737)
-- Dependencies: 166 1941 152
-- Name: target_check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id);


--
-- TOC entry 1997 (class 2606 OID 16742)
-- Dependencies: 149 1937 166
-- Name: target_check_solutions_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1998 (class 2606 OID 16747)
-- Dependencies: 168 1967 166
-- Name: target_check_solutions_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1999 (class 2606 OID 16793)
-- Dependencies: 152 167 1941
-- Name: target_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2000 (class 2606 OID 16798)
-- Dependencies: 1951 167 157
-- Name: target_checks_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2001 (class 2606 OID 16803)
-- Dependencies: 1967 168 167
-- Name: target_checks_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2002 (class 2606 OID 16767)
-- Dependencies: 168 161 1955
-- Name: targets_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2003 (class 2606 OID 16772)
-- Dependencies: 170 155 1945
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


-- Completed on 2012-07-07 15:24:20 MSK

--
-- PostgreSQL database dump complete
--

