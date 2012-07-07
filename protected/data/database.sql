--
-- PostgreSQL database dump
--

-- Dumped from database version 8.4.12
-- Dumped by pg_dump version 9.1.3
-- Started on 2012-07-07 16:54:23 MSK

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

INSERT INTO check_categories VALUES (3, 'DNS');
INSERT INTO check_categories VALUES (14, 'SMTP');
INSERT INTO check_categories VALUES (15, 'TCP');
INSERT INTO check_categories VALUES (16, 'Web');
INSERT INTO check_categories VALUES (17, 'FTP');
INSERT INTO check_categories VALUES (18, 'SSH');


--
-- TOC entry 2005 (class 0 OID 16417)
-- Dependencies: 142
-- Data for Name: check_categories_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

INSERT INTO check_categories_l10n VALUES (3, 3, 'DNS');
INSERT INTO check_categories_l10n VALUES (3, 4, 'DNS');
INSERT INTO check_categories_l10n VALUES (14, 3, 'SMTP');
INSERT INTO check_categories_l10n VALUES (14, 4, '');
INSERT INTO check_categories_l10n VALUES (15, 3, 'TCP');
INSERT INTO check_categories_l10n VALUES (15, 4, '');
INSERT INTO check_categories_l10n VALUES (16, 3, 'Web');
INSERT INTO check_categories_l10n VALUES (16, 4, '');
INSERT INTO check_categories_l10n VALUES (17, 3, 'FTP');
INSERT INTO check_categories_l10n VALUES (17, 4, '');
INSERT INTO check_categories_l10n VALUES (18, 3, 'SSH');
INSERT INTO check_categories_l10n VALUES (18, 4, '');


--
-- TOC entry 2006 (class 0 OID 16423)
-- Dependencies: 143
-- Data for Name: check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

INSERT INTO check_inputs VALUES (11, 28, 'Recipient', '', 0, '');
INSERT INTO check_inputs VALUES (12, 28, 'Server', '', 1, '');
INSERT INTO check_inputs VALUES (13, 28, 'Login', '', 2, '');
INSERT INTO check_inputs VALUES (14, 28, 'Password', '', 3, '');
INSERT INTO check_inputs VALUES (15, 28, 'Sender', '', 4, '');
INSERT INTO check_inputs VALUES (16, 28, 'Folder', '', 5, '');
INSERT INTO check_inputs VALUES (19, 31, 'Page Type', '', 2, 'php');
INSERT INTO check_inputs VALUES (20, 31, 'Cookies', '', 2, '');
INSERT INTO check_inputs VALUES (21, 31, 'URL Limit', '', 3, '100');
INSERT INTO check_inputs VALUES (23, 22, 'Hostname', '', 0, '');
INSERT INTO check_inputs VALUES (25, 24, 'Hostname', '', 0, '');
INSERT INTO check_inputs VALUES (26, 25, 'Hostname', '', 0, '');
INSERT INTO check_inputs VALUES (8, 23, 'Show All', '', 0, '0');
INSERT INTO check_inputs VALUES (27, 32, 'Range Count', '', 0, '10');
INSERT INTO check_inputs VALUES (28, 34, 'Timeout', '', 0, '120');
INSERT INTO check_inputs VALUES (29, 34, 'Debug', '', 1, '0');
INSERT INTO check_inputs VALUES (30, 35, 'Timeout', '', 0, '120');
INSERT INTO check_inputs VALUES (31, 35, 'Debug', '', 1, '0');
INSERT INTO check_inputs VALUES (33, 40, 'Users', '', 0, '');
INSERT INTO check_inputs VALUES (34, 40, 'Passwords', '', 1, '');
INSERT INTO check_inputs VALUES (35, 46, 'Code', 'Possible values: php, cfm, asp', 0, 'php');
INSERT INTO check_inputs VALUES (36, 47, 'Timeout', 'DNS timeout.', 0, '10');
INSERT INTO check_inputs VALUES (37, 47, 'Max Results', 'Maximum number of results', 1, '100');
INSERT INTO check_inputs VALUES (38, 47, 'Mode', 'Operation mode: 0 - output generated list only, 1 - resolve IP check', 2, '0');
INSERT INTO check_inputs VALUES (39, 51, 'Port Range', '2 lines: start and end of the range.', 0, '1
80');
INSERT INTO check_inputs VALUES (40, 52, 'Port Range', 'Port range that will be passed to nmap. Please use nmap syntax for -p command line argument (for example, 22; 1-65535; U:53,111,137,T:21-25,80,139,8080)', 0, '');
INSERT INTO check_inputs VALUES (41, 52, 'Timeout', 'Timeout in milliseconds.', 1, '1000');
INSERT INTO check_inputs VALUES (42, 53, 'Timeout', 'SMTP server connection timeout', 0, '10');
INSERT INTO check_inputs VALUES (44, 53, 'Destination E-mail', '', 2, 'destination@gmail.com');
INSERT INTO check_inputs VALUES (43, 53, 'Source E-mail', '', 1, 'source@gmail.com');
INSERT INTO check_inputs VALUES (45, 54, 'Users', '', 0, '');
INSERT INTO check_inputs VALUES (46, 54, 'Passwords', '', 1, '');
INSERT INTO check_inputs VALUES (32, 39, 'Long List', '0 - use small list, 1 - use long list.', 0, '0');
INSERT INTO check_inputs VALUES (47, 55, 'Long List', '0 - use small list, 1 - use long list.', 0, '');
INSERT INTO check_inputs VALUES (48, 56, 'URLs', 'List of URLs.', 0, '');
INSERT INTO check_inputs VALUES (49, 60, 'Timeout', '', 0, '10');
INSERT INTO check_inputs VALUES (50, 61, 'Paths', '', 0, '');
INSERT INTO check_inputs VALUES (51, 62, 'Paths', '', 0, '');
INSERT INTO check_inputs VALUES (52, 63, 'Paths', '', 0, '');
INSERT INTO check_inputs VALUES (22, 1, 'Hostname', '', 0, '0');


--
-- TOC entry 2007 (class 0 OID 16432)
-- Dependencies: 145
-- Data for Name: check_inputs_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

INSERT INTO check_inputs_l10n VALUES (50, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (51, 3, 'Paths', '', '');
INSERT INTO check_inputs_l10n VALUES (11, 3, 'Recipient', '', '');
INSERT INTO check_inputs_l10n VALUES (11, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (12, 3, 'Server', '', '');
INSERT INTO check_inputs_l10n VALUES (12, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (13, 3, 'Login', '', '');
INSERT INTO check_inputs_l10n VALUES (13, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (14, 3, 'Password', '', '');
INSERT INTO check_inputs_l10n VALUES (14, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (15, 3, 'Sender', '', '');
INSERT INTO check_inputs_l10n VALUES (15, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (16, 3, 'Folder', '', '');
INSERT INTO check_inputs_l10n VALUES (16, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (19, 3, 'Page Type', '', 'php');
INSERT INTO check_inputs_l10n VALUES (19, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (20, 3, 'Cookies', '', '');
INSERT INTO check_inputs_l10n VALUES (20, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (21, 3, 'URL Limit', '', '100');
INSERT INTO check_inputs_l10n VALUES (21, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (23, 3, 'Hostname', '', '');
INSERT INTO check_inputs_l10n VALUES (23, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (25, 3, 'Hostname', '', '');
INSERT INTO check_inputs_l10n VALUES (25, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (26, 3, 'Hostname', '', '');
INSERT INTO check_inputs_l10n VALUES (26, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (8, 3, 'Show All', '', '0');
INSERT INTO check_inputs_l10n VALUES (8, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (27, 3, 'Range Count', '', '10');
INSERT INTO check_inputs_l10n VALUES (27, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (28, 3, 'Timeout', '', '120');
INSERT INTO check_inputs_l10n VALUES (28, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (29, 3, 'Debug', '', '0');
INSERT INTO check_inputs_l10n VALUES (29, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (30, 3, 'Timeout', '', '120');
INSERT INTO check_inputs_l10n VALUES (30, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (31, 3, 'Debug', '', '0');
INSERT INTO check_inputs_l10n VALUES (31, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (33, 3, 'Users', '', '');
INSERT INTO check_inputs_l10n VALUES (33, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (34, 3, 'Passwords', '', '');
INSERT INTO check_inputs_l10n VALUES (34, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (35, 3, 'Code', 'Possible values: php, cfm, asp', 'php');
INSERT INTO check_inputs_l10n VALUES (35, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (36, 3, 'Timeout', 'DNS timeout.', '10');
INSERT INTO check_inputs_l10n VALUES (36, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (37, 3, 'Max Results', 'Maximum number of results', '100');
INSERT INTO check_inputs_l10n VALUES (37, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (38, 3, 'Mode', 'Operation mode: 0 - output generated list only, 1 - resolve IP check', '0');
INSERT INTO check_inputs_l10n VALUES (38, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (39, 3, 'Port Range', '2 lines: start and end of the range.', '1
80');
INSERT INTO check_inputs_l10n VALUES (39, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (40, 3, 'Port Range', 'Port range that will be passed to nmap. Please use nmap syntax for -p command line argument (for example, 22; 1-65535; U:53,111,137,T:21-25,80,139,8080)', '');
INSERT INTO check_inputs_l10n VALUES (40, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (41, 3, 'Timeout', 'Timeout in milliseconds.', '1000');
INSERT INTO check_inputs_l10n VALUES (41, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (42, 3, 'Timeout', 'SMTP server connection timeout', '10');
INSERT INTO check_inputs_l10n VALUES (42, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (44, 3, 'Destination E-mail', '', 'destination@gmail.com');
INSERT INTO check_inputs_l10n VALUES (44, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (43, 3, 'Source E-mail', '', 'source@gmail.com');
INSERT INTO check_inputs_l10n VALUES (43, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (45, 3, 'Users', '', '');
INSERT INTO check_inputs_l10n VALUES (45, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (46, 3, 'Passwords', '', '');
INSERT INTO check_inputs_l10n VALUES (46, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (32, 3, 'Long List', '0 - use small list, 1 - use long list.', '0');
INSERT INTO check_inputs_l10n VALUES (32, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (47, 3, 'Long List', '0 - use small list, 1 - use long list.', '');
INSERT INTO check_inputs_l10n VALUES (47, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (48, 3, 'URLs', 'List of URLs.', '');
INSERT INTO check_inputs_l10n VALUES (48, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (49, 3, 'Timeout', '', '10');
INSERT INTO check_inputs_l10n VALUES (49, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (50, 3, 'Paths', '', '');
INSERT INTO check_inputs_l10n VALUES (51, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (52, 3, 'Paths', '', '');
INSERT INTO check_inputs_l10n VALUES (52, 4, '', '', '');
INSERT INTO check_inputs_l10n VALUES (22, 3, 'Hostname', '', '0');
INSERT INTO check_inputs_l10n VALUES (22, 4, '', '', '');

--
-- TOC entry 2012 (class 0 OID 16468)
-- Dependencies: 152
-- Data for Name: checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

INSERT INTO checks VALUES (33, 3, 'DNS AFXR', '', '', '', false, true, 'dns_afxr.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (34, 3, 'DNS DOM MX', '', '', '', false, true, 'dns_dom_mx.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (35, 3, 'DNS Find NS', '', '', '', false, true, 'dns_find_ns.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (23, 3, 'DNS Hosting', '', '', '', false, true, 'dns_hosting.py', false, NULL, NULL, NULL, NULL);
INSERT INTO checks VALUES (24, 3, 'DNS SOA', '', '', '', false, true, 'dns_soa.py', false, NULL, NULL, NULL, NULL);
INSERT INTO checks VALUES (25, 3, 'DNS SPF', '', '', '', false, true, 'dns_spf.py', false, NULL, NULL, NULL, NULL);
INSERT INTO checks VALUES (26, 14, 'SMTP Banner', '', '', '', false, true, 'smtp_banner.py', false, NULL, NULL, NULL, NULL);
INSERT INTO checks VALUES (27, 14, 'SMTP DNSBL', '', '', '', false, true, 'smtp_dnsbl.py', false, NULL, NULL, NULL, NULL);
INSERT INTO checks VALUES (28, 14, 'SMTP Filter', '', '', '', false, true, 'smtp_filter.py', false, NULL, NULL, NULL, NULL);
INSERT INTO checks VALUES (30, 16, 'Web HTTP Methods', '', '', '', false, true, 'web_http_methods.py', false, NULL, NULL, NULL, NULL);
INSERT INTO checks VALUES (31, 16, 'Web SQL XSS', '', '', '', false, true, 'web_sql_xss.py', false, NULL, NULL, NULL, NULL);
INSERT INTO checks VALUES (36, 3, 'DNS IP Range', '', '', '', false, true, 'dns_ip_range.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (47, 3, 'DNS NIC Typosquatting', '', '', '', false, true, 'nic_typosquatting.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (48, 3, 'DNS NIC Whois', '', '', '', false, true, 'nic_whois.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (29, 15, 'TCP Traceroute', '', '', '', false, true, 'tcp_traceroute.py', false, '', 80, NULL, NULL);
INSERT INTO checks VALUES (50, 3, 'DNS NS Version', '', '', '', false, true, 'ns_version.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (37, 3, 'DNS Resolve IP', '', '', '', false, true, 'dns_resolve_ip.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (22, 3, 'DNS A (Non-Recursive)', '', '', '', false, true, 'dns_a_nr.py', true, '', NULL, NULL, NULL);
INSERT INTO checks VALUES (40, 17, 'FTP Bruteforce', '', '', '', false, true, 'ftp_bruteforce.pl', false, '', NULL, NULL, NULL);
INSERT INTO checks VALUES (54, 18, 'SSH Bruteforce', '', '', '', false, true, 'ssh_bruteforce.pl', false, '', NULL, NULL, NULL);
INSERT INTO checks VALUES (38, 3, 'DNS SPF (Perl)', '', '', '', false, true, 'dns_spf.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (55, 3, 'DNS Subdomain Bruteforce', '', '', '', false, true, 'subdomain_bruteforce.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (39, 3, 'DNS Top TLDs', '', '', '', false, true, 'dns_top_tlds.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (53, 14, 'SMTP Relay', '', '', '', false, true, 'smtp_relay.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (1, 3, 'DNS A', '', '', '', false, true, 'dns_a.py', false, '', NULL, '', '');
INSERT INTO checks VALUES (52, 15, 'Nmap Port Scan', '', '', '', false, true, 'pscan.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (51, 15, 'TCP Port Scan', '', '', '', false, true, 'portscan.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (32, 16, 'Apache DoS', '', '', '', false, true, 'apache_dos.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (41, 16, 'Fuzz Check', '', '', '', false, true, 'fuzz_check.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (42, 16, 'Google URL', '', '', '', false, true, 'google_url.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (43, 16, 'Grep URL', '', '', '', false, true, 'grep_url.pl', false, 'http', NULL, '', '');
INSERT INTO checks VALUES (44, 16, 'HTTP Banner', '', '', '', false, true, 'http_banner.pl', false, 'http', NULL, '', '');
INSERT INTO checks VALUES (45, 16, 'Joomla Scan', '', '', '', false, true, 'joomla_scan.pl', false, 'http', NULL, '', '');
INSERT INTO checks VALUES (46, 16, 'Login Pages', '', '', '', false, true, 'login_pages.pl', false, 'http', NULL, '', '');
INSERT INTO checks VALUES (49, 16, 'Nikto', '', '', '', false, true, 'nikto.pl', false, 'http', 80, '', '');
INSERT INTO checks VALUES (56, 16, 'URL Scan', '', '', '', false, true, 'urlscan.pl', false, 'http', NULL, '', '');
INSERT INTO checks VALUES (61, 16, 'Web Auth Scanner', '', '', '', false, true, 'www_auth_scanner.pl', false, 'http', 80, '', '');
INSERT INTO checks VALUES (62, 16, 'Web Directory Scanner', '', '', '', false, true, 'www_dir_scanner.pl', false, 'http', 80, '', '');
INSERT INTO checks VALUES (63, 16, 'Web File Scanner', '', '', '', false, true, 'www_file_scanner.pl', false, 'http', 80, '', '');
INSERT INTO checks VALUES (57, 16, 'Web Server CMS', '', '', '', false, true, 'webserver_cms.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (58, 16, 'Web Server Error Message', '', '', '', false, true, 'webserver_error_msg.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (60, 16, 'Web Server SSL', '', '', '', false, true, 'webserver_ssl.pl', false, '', NULL, '', '');
INSERT INTO checks VALUES (59, 16, 'Web Server Files', '', '', '', false, true, 'webserver_files.pl', false, '', NULL, '', '');


--
-- TOC entry 2013 (class 0 OID 16476)
-- Dependencies: 154
-- Data for Name: checks_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

INSERT INTO checks_l10n VALUES (54, 3, 'SSH Bruteforce', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (54, 4, '', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (23, 3, 'DNS Hosting', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (23, 4, '', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (24, 3, 'DNS SOA', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (24, 4, '', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (25, 3, 'DNS SPF', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (25, 4, '', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (26, 3, 'SMTP Banner', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (26, 4, '', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (27, 3, 'SMTP DNSBL', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (27, 4, '', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (28, 3, 'SMTP Filter', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (28, 4, '', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (30, 3, 'Web HTTP Methods', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (30, 4, '', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (31, 3, 'Web SQL XSS', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (31, 4, '', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (29, 3, 'TCP Traceroute', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (29, 4, '', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (47, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (48, 3, 'DNS NIC Whois', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (22, 3, 'DNS A (Non-Recursive)', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (22, 4, '', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (48, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (50, 3, 'DNS NS Version', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (49, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (56, 3, 'URL Scan', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (56, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (61, 3, 'Web Auth Scanner', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (40, 3, 'FTP Bruteforce', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (40, 4, '', '', '', '', NULL, NULL);
INSERT INTO checks_l10n VALUES (61, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (62, 3, 'Web Directory Scanner', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (62, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (63, 3, 'Web File Scanner', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (50, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (63, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (57, 3, 'Web Server CMS', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (57, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (58, 3, 'Web Server Error Message', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (58, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (60, 3, 'Web Server SSL', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (60, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (59, 3, 'Web Server Files', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (37, 3, 'DNS Resolve IP', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (37, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (59, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (38, 3, 'DNS SPF (Perl)', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (38, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (55, 3, 'DNS Subdomain Bruteforce', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (55, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (39, 3, 'DNS Top TLDs', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (39, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (53, 3, 'SMTP Relay', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (53, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (33, 3, 'DNS AFXR', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (33, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (34, 3, 'DNS DOM MX', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (34, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (35, 3, 'DNS Find NS', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (35, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (36, 3, 'DNS IP Range', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (36, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (47, 3, 'DNS NIC Typosquatting', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (1, 3, 'DNS A', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (1, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (52, 3, 'Nmap Port Scan', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (52, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (51, 3, 'TCP Port Scan', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (51, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (32, 3, 'Apache DoS', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (32, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (41, 3, 'Fuzz Check', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (41, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (42, 3, 'Google URL', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (42, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (43, 3, 'Grep URL', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (43, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (44, 3, 'HTTP Banner', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (44, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (45, 3, 'Joomla Scan', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (45, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (46, 3, 'Login Pages', NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (46, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO checks_l10n VALUES (49, 3, 'Nikto', NULL, NULL, NULL, NULL, NULL);


--
-- TOC entry 2014 (class 0 OID 16482)
-- Dependencies: 155
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: gtta
--



--
-- TOC entry 2015 (class 0 OID 16490)
-- Dependencies: 157
-- Data for Name: languages; Type: TABLE DATA; Schema: public; Owner: gtta
--

INSERT INTO languages VALUES (3, 'English', 'en', true);
INSERT INTO languages VALUES (4, 'Deutsch', 'de', false);


--
-- TOC entry 2016 (class 0 OID 16498)
-- Dependencies: 159
-- Data for Name: project_details; Type: TABLE DATA; Schema: public; Owner: gtta
--



--
-- TOC entry 2017 (class 0 OID 16506)
-- Dependencies: 161
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: gtta
--



--
-- TOC entry 2018 (class 0 OID 16515)
-- Dependencies: 163
-- Data for Name: target_check_attachments; Type: TABLE DATA; Schema: public; Owner: gtta
--



--
-- TOC entry 2019 (class 0 OID 16522)
-- Dependencies: 164
-- Data for Name: target_check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--



--
-- TOC entry 2020 (class 0 OID 16530)
-- Dependencies: 165
-- Data for Name: target_check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--



--
-- TOC entry 2021 (class 0 OID 16536)
-- Dependencies: 166
-- Data for Name: target_check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--



--
-- TOC entry 2022 (class 0 OID 16539)
-- Dependencies: 167
-- Data for Name: target_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--



--
-- TOC entry 2023 (class 0 OID 16546)
-- Dependencies: 168
-- Data for Name: targets; Type: TABLE DATA; Schema: public; Owner: gtta
--



--
-- TOC entry 2024 (class 0 OID 16554)
-- Dependencies: 170
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: gtta
--

INSERT INTO users VALUES (1, 'test@admin.com', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '', NULL, 'admin');
INSERT INTO users VALUES (2, 'test@user.com', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', '', NULL, 'user');


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


-- Completed on 2012-07-07 16:54:23 MSK

--
-- PostgreSQL database dump complete
--

