--
-- PostgreSQL database dump
--

-- Dumped from database version 8.4.12
-- Dumped by pg_dump version 9.1.3
-- Started on 2012-07-31 15:54:23 MSK

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- TOC entry 480 (class 1247 OID 21583)
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
-- TOC entry 483 (class 1247 OID 21590)
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
-- TOC entry 486 (class 1247 OID 21596)
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
-- TOC entry 489 (class 1247 OID 21601)
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
-- TOC entry 140 (class 1259 OID 21605)
-- Dependencies: 6
-- Name: check_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE check_categories (
    id bigint NOT NULL,
    name character varying(1000) NOT NULL
);


ALTER TABLE public.check_categories OWNER TO gtta;

--
-- TOC entry 141 (class 1259 OID 21611)
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
-- TOC entry 2120 (class 0 OID 0)
-- Dependencies: 141
-- Name: check_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_categories_id_seq OWNED BY check_categories.id;


--
-- TOC entry 2121 (class 0 OID 0)
-- Dependencies: 141
-- Name: check_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_categories_id_seq', 6, true);


--
-- TOC entry 142 (class 1259 OID 21613)
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
-- TOC entry 143 (class 1259 OID 21619)
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
-- TOC entry 144 (class 1259 OID 21625)
-- Dependencies: 143 6
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
-- TOC entry 2122 (class 0 OID 0)
-- Dependencies: 144
-- Name: check_controls_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_controls_id_seq OWNED BY check_controls.id;


--
-- TOC entry 2123 (class 0 OID 0)
-- Dependencies: 144
-- Name: check_controls_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_controls_id_seq', 9, true);


--
-- TOC entry 145 (class 1259 OID 21627)
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
-- TOC entry 146 (class 1259 OID 21633)
-- Dependencies: 1952 6
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
-- TOC entry 147 (class 1259 OID 21640)
-- Dependencies: 146 6
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
-- TOC entry 2124 (class 0 OID 0)
-- Dependencies: 147
-- Name: check_inputs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_inputs_id_seq OWNED BY check_inputs.id;


--
-- TOC entry 2125 (class 0 OID 0)
-- Dependencies: 147
-- Name: check_inputs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_inputs_id_seq', 40, true);


--
-- TOC entry 148 (class 1259 OID 21642)
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
-- TOC entry 149 (class 1259 OID 21648)
-- Dependencies: 1954 6
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
-- TOC entry 150 (class 1259 OID 21655)
-- Dependencies: 149 6
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
-- TOC entry 2126 (class 0 OID 0)
-- Dependencies: 150
-- Name: check_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_results_id_seq OWNED BY check_results.id;


--
-- TOC entry 2127 (class 0 OID 0)
-- Dependencies: 150
-- Name: check_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_results_id_seq', 1, false);


--
-- TOC entry 151 (class 1259 OID 21657)
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
-- TOC entry 152 (class 1259 OID 21663)
-- Dependencies: 1956 6
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
-- TOC entry 153 (class 1259 OID 21670)
-- Dependencies: 6 152
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
-- TOC entry 2128 (class 0 OID 0)
-- Dependencies: 153
-- Name: check_solutions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_solutions_id_seq OWNED BY check_solutions.id;


--
-- TOC entry 2129 (class 0 OID 0)
-- Dependencies: 153
-- Name: check_solutions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_solutions_id_seq', 1, false);


--
-- TOC entry 154 (class 1259 OID 21672)
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
-- TOC entry 155 (class 1259 OID 21678)
-- Dependencies: 1958 6
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
    reference_url character varying(1000),
    effort integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.checks OWNER TO gtta;

--
-- TOC entry 156 (class 1259 OID 21685)
-- Dependencies: 6 155
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
-- TOC entry 2130 (class 0 OID 0)
-- Dependencies: 156
-- Name: checks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE checks_id_seq OWNED BY checks.id;


--
-- TOC entry 2131 (class 0 OID 0)
-- Dependencies: 156
-- Name: checks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('checks_id_seq', 43, true);


--
-- TOC entry 157 (class 1259 OID 21687)
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
-- TOC entry 158 (class 1259 OID 21693)
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
-- TOC entry 159 (class 1259 OID 21699)
-- Dependencies: 158 6
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
-- TOC entry 2132 (class 0 OID 0)
-- Dependencies: 159
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE clients_id_seq OWNED BY clients.id;


--
-- TOC entry 2133 (class 0 OID 0)
-- Dependencies: 159
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('clients_id_seq', 2, true);


--
-- TOC entry 160 (class 1259 OID 21701)
-- Dependencies: 1961 1962 6
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
-- TOC entry 161 (class 1259 OID 21709)
-- Dependencies: 160 6
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
-- TOC entry 2134 (class 0 OID 0)
-- Dependencies: 161
-- Name: emails_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE emails_id_seq OWNED BY emails.id;


--
-- TOC entry 2135 (class 0 OID 0)
-- Dependencies: 161
-- Name: emails_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('emails_id_seq', 1, false);


--
-- TOC entry 162 (class 1259 OID 21711)
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
-- TOC entry 163 (class 1259 OID 21717)
-- Dependencies: 162 6
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
-- TOC entry 2136 (class 0 OID 0)
-- Dependencies: 163
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE languages_id_seq OWNED BY languages.id;


--
-- TOC entry 2137 (class 0 OID 0)
-- Dependencies: 163
-- Name: languages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('languages_id_seq', 3, false);


--
-- TOC entry 164 (class 1259 OID 21719)
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
-- TOC entry 165 (class 1259 OID 21725)
-- Dependencies: 164 6
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
-- TOC entry 2138 (class 0 OID 0)
-- Dependencies: 165
-- Name: project_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE project_details_id_seq OWNED BY project_details.id;


--
-- TOC entry 2139 (class 0 OID 0)
-- Dependencies: 165
-- Name: project_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('project_details_id_seq', 1, false);


--
-- TOC entry 166 (class 1259 OID 21727)
-- Dependencies: 1966 486 6
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
-- TOC entry 167 (class 1259 OID 21734)
-- Dependencies: 6 166
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
-- TOC entry 2140 (class 0 OID 0)
-- Dependencies: 167
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE projects_id_seq OWNED BY projects.id;


--
-- TOC entry 2141 (class 0 OID 0)
-- Dependencies: 167
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('projects_id_seq', 2, true);


--
-- TOC entry 168 (class 1259 OID 21736)
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
-- TOC entry 169 (class 1259 OID 21742)
-- Dependencies: 168 6
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
-- TOC entry 2142 (class 0 OID 0)
-- Dependencies: 169
-- Name: references_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE references_id_seq OWNED BY "references".id;


--
-- TOC entry 2143 (class 0 OID 0)
-- Dependencies: 169
-- Name: references_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('references_id_seq', 1, true);


--
-- TOC entry 183 (class 1259 OID 22073)
-- Dependencies: 6
-- Name: risk_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_categories (
    id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_categories OWNER TO gtta;

--
-- TOC entry 182 (class 1259 OID 22071)
-- Dependencies: 183 6
-- Name: risk_categories_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE risk_categories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.risk_categories_id_seq OWNER TO gtta;

--
-- TOC entry 2144 (class 0 OID 0)
-- Dependencies: 182
-- Name: risk_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE risk_categories_id_seq OWNED BY risk_categories.id;


--
-- TOC entry 2145 (class 0 OID 0)
-- Dependencies: 182
-- Name: risk_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('risk_categories_id_seq', 7, true);


--
-- TOC entry 184 (class 1259 OID 22082)
-- Dependencies: 6
-- Name: risk_categories_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_categories_l10n (
    risk_category_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_categories_l10n OWNER TO gtta;

--
-- TOC entry 170 (class 1259 OID 21744)
-- Dependencies: 6
-- Name: system; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE system (
    id bigint NOT NULL,
    backup timestamp without time zone
);


ALTER TABLE public.system OWNER TO gtta;

--
-- TOC entry 171 (class 1259 OID 21747)
-- Dependencies: 6 170
-- Name: system_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE system_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.system_id_seq OWNER TO gtta;

--
-- TOC entry 2146 (class 0 OID 0)
-- Dependencies: 171
-- Name: system_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE system_id_seq OWNED BY system.id;


--
-- TOC entry 2147 (class 0 OID 0)
-- Dependencies: 171
-- Name: system_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('system_id_seq', 1, true);


--
-- TOC entry 172 (class 1259 OID 21749)
-- Dependencies: 1970 6
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
-- TOC entry 173 (class 1259 OID 21756)
-- Dependencies: 1971 1972 1973 1974 1975 6
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
-- TOC entry 174 (class 1259 OID 21764)
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
-- TOC entry 175 (class 1259 OID 21770)
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
-- TOC entry 176 (class 1259 OID 21773)
-- Dependencies: 1976 6 483 480
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
-- TOC entry 177 (class 1259 OID 21780)
-- Dependencies: 6
-- Name: target_references; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE target_references (
    target_id bigint NOT NULL,
    reference_id bigint NOT NULL
);


ALTER TABLE public.target_references OWNER TO gtta;

--
-- TOC entry 178 (class 1259 OID 21783)
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
-- TOC entry 179 (class 1259 OID 21789)
-- Dependencies: 6 178
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
-- TOC entry 2148 (class 0 OID 0)
-- Dependencies: 179
-- Name: targets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE targets_id_seq OWNED BY targets.id;


--
-- TOC entry 2149 (class 0 OID 0)
-- Dependencies: 179
-- Name: targets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('targets_id_seq', 3, true);


--
-- TOC entry 180 (class 1259 OID 21791)
-- Dependencies: 1978 6 489
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
-- TOC entry 181 (class 1259 OID 21798)
-- Dependencies: 180 6
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
-- TOC entry 2150 (class 0 OID 0)
-- Dependencies: 181
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- TOC entry 2151 (class 0 OID 0)
-- Dependencies: 181
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('users_id_seq', 2, false);


--
-- TOC entry 1950 (class 2604 OID 21800)
-- Dependencies: 141 140
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories ALTER COLUMN id SET DEFAULT nextval('check_categories_id_seq'::regclass);


--
-- TOC entry 1951 (class 2604 OID 21801)
-- Dependencies: 144 143
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls ALTER COLUMN id SET DEFAULT nextval('check_controls_id_seq'::regclass);


--
-- TOC entry 1953 (class 2604 OID 21802)
-- Dependencies: 147 146
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs ALTER COLUMN id SET DEFAULT nextval('check_inputs_id_seq'::regclass);


--
-- TOC entry 1955 (class 2604 OID 21803)
-- Dependencies: 150 149
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results ALTER COLUMN id SET DEFAULT nextval('check_results_id_seq'::regclass);


--
-- TOC entry 1957 (class 2604 OID 21804)
-- Dependencies: 153 152
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions ALTER COLUMN id SET DEFAULT nextval('check_solutions_id_seq'::regclass);


--
-- TOC entry 1959 (class 2604 OID 21805)
-- Dependencies: 156 155
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks ALTER COLUMN id SET DEFAULT nextval('checks_id_seq'::regclass);


--
-- TOC entry 1960 (class 2604 OID 21806)
-- Dependencies: 159 158
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY clients ALTER COLUMN id SET DEFAULT nextval('clients_id_seq'::regclass);


--
-- TOC entry 1963 (class 2604 OID 21807)
-- Dependencies: 161 160
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails ALTER COLUMN id SET DEFAULT nextval('emails_id_seq'::regclass);


--
-- TOC entry 1964 (class 2604 OID 21808)
-- Dependencies: 163 162
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY languages ALTER COLUMN id SET DEFAULT nextval('languages_id_seq'::regclass);


--
-- TOC entry 1965 (class 2604 OID 21809)
-- Dependencies: 165 164
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details ALTER COLUMN id SET DEFAULT nextval('project_details_id_seq'::regclass);


--
-- TOC entry 1967 (class 2604 OID 21810)
-- Dependencies: 167 166
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects ALTER COLUMN id SET DEFAULT nextval('projects_id_seq'::regclass);


--
-- TOC entry 1968 (class 2604 OID 21811)
-- Dependencies: 169 168
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY "references" ALTER COLUMN id SET DEFAULT nextval('references_id_seq'::regclass);


--
-- TOC entry 1980 (class 2604 OID 22076)
-- Dependencies: 182 183 183
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories ALTER COLUMN id SET DEFAULT nextval('risk_categories_id_seq'::regclass);


--
-- TOC entry 1969 (class 2604 OID 21812)
-- Dependencies: 171 170
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY system ALTER COLUMN id SET DEFAULT nextval('system_id_seq'::regclass);


--
-- TOC entry 1977 (class 2604 OID 21813)
-- Dependencies: 179 178
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets ALTER COLUMN id SET DEFAULT nextval('targets_id_seq'::regclass);


--
-- TOC entry 1979 (class 2604 OID 21814)
-- Dependencies: 181 180
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- TOC entry 2086 (class 0 OID 21605)
-- Dependencies: 140
-- Data for Name: check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_categories (id, name) FROM stdin;
1	DNS
2	FTP
3	SMTP
4	SSH
5	TCP
6	Web Anonymous
\.


--
-- TOC entry 2087 (class 0 OID 21613)
-- Dependencies: 142
-- Data for Name: check_categories_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_categories_l10n (check_category_id, language_id, name) FROM stdin;
1	1	DNS
1	2	\N
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
\.


--
-- TOC entry 2088 (class 0 OID 21619)
-- Dependencies: 143
-- Data for Name: check_controls; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_controls (id, check_category_id, name) FROM stdin;
1	1	Default
2	2	Default
3	3	Default
4	4	Default
5	5	Default
6	6	Default
7	1	This is a long name of the control
8	1	Session Handling
9	1	Some other important stuff
\.


--
-- TOC entry 2089 (class 0 OID 21627)
-- Dependencies: 145
-- Data for Name: check_controls_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_controls_l10n (check_control_id, language_id, name) FROM stdin;
1	1	Default
1	2	\N
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
8	1	Session Handling
8	2	\N
9	1	Some other important stuff
9	2	\N
\.


--
-- TOC entry 2090 (class 0 OID 21633)
-- Dependencies: 146
-- Data for Name: check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs (id, check_id, name, description, sort_order, value) FROM stdin;
1	1	Hostname		0	
2	2	Hostname		0	
3	4	Timeout		0	120
4	4	Debug		1	1
5	5	Timeout		0	120
6	5	Debug		1	1
7	6	Show All		0	0
8	8	Timeout		0	10
9	8	Max Results		1	100
10	8	Mode	Operation mode: 0 - output generated list only, 1 - resolve IP check	2	1
11	12	Hostname		0	
12	13	Hostname		0	
13	15	Long List		0	0
14	16	Long List		0	0
15	17	Users		0	
16	17	Passwords		1	
17	20	Recipient		0	
18	20	Server		1	
19	20	Login		2	
20	20	Password		3	
21	20	Sender		4	
22	20	Folder		5	
23	21	Timeout		0	10
24	21	Source E-mail		1	source@gmail.com
25	21	Destination E-mail		2	destination@gmail.com
26	22	Users		0	
27	22	Passwords		1	
28	23	Port Range	Port range that will be passed to nmap. Please use nmap syntax for -p command line argument (for example, 22; 1-65535; U:53,111,137,T:21-25,80,139,8080)	0	
29	23	Timeout	Timeout in milliseconds.	1	1000
30	24	Port Range	2 lines: start and end of the range.	0	1\r\n80
31	26	Range Count		0	10
32	32	Code	Possible values: php, cfm, asp.	0	php
34	35	Paths		0	
33	34	URLs		0	
35	36	Paths		0	
36	37	Paths		0	
37	42	Timeout		0	10
38	43	Page Type	Possible values: php, asp.	0	php
39	43	Cookies		1	
40	43	URL Limit		2	100
\.


--
-- TOC entry 2091 (class 0 OID 21642)
-- Dependencies: 148
-- Data for Name: check_inputs_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs_l10n (check_input_id, language_id, name, description, value) FROM stdin;
1	1	Hostname	\N	\N
1	2	\N	\N	\N
2	1	Hostname	\N	\N
2	2	\N	\N	\N
3	1	Timeout	\N	120
3	2	\N	\N	\N
4	1	Debug	\N	1
4	2	\N	\N	\N
5	1	Timeout	\N	120
5	2	\N	\N	\N
6	1	Debug	\N	1
6	2	\N	\N	\N
7	1	Show All	\N	0
7	2	\N	\N	\N
8	1	Timeout	\N	10
8	2	\N	\N	\N
9	1	Max Results	\N	100
9	2	\N	\N	\N
10	1	Mode	Operation mode: 0 - output generated list only, 1 - resolve IP check	1
10	2	\N	\N	\N
11	1	Hostname	\N	\N
11	2	\N	\N	\N
12	1	Hostname	\N	\N
12	2	\N	\N	\N
13	1	Long List	\N	0
13	2	\N	\N	\N
14	1	Long List	\N	0
14	2	\N	\N	\N
15	1	Users	\N	\N
15	2	\N	\N	\N
16	1	Passwords	\N	\N
16	2	\N	\N	\N
17	1	Recipient	\N	\N
17	2	\N	\N	\N
18	1	Server	\N	\N
18	2	\N	\N	\N
19	1	Login	\N	\N
19	2	\N	\N	\N
20	1	Password	\N	\N
20	2	\N	\N	\N
21	1	Sender	\N	\N
21	2	\N	\N	\N
22	1	Folder	\N	\N
22	2	\N	\N	\N
23	1	Timeout	\N	10
23	2	\N	\N	\N
24	1	Source E-mail	\N	source@gmail.com
24	2	\N	\N	\N
25	1	Destination E-mail	\N	destination@gmail.com
25	2	\N	\N	\N
26	1	Users	\N	\N
26	2	\N	\N	\N
27	1	Passwords	\N	\N
27	2	\N	\N	\N
28	1	Port Range	Port range that will be passed to nmap. Please use nmap syntax for -p command line argument (for example, 22; 1-65535; U:53,111,137,T:21-25,80,139,8080)	\N
28	2	\N	\N	\N
29	1	Timeout	Timeout in milliseconds.	1000
29	2	\N	\N	\N
30	1	Port Range	2 lines: start and end of the range.	1\r\n80
30	2	\N	\N	\N
31	1	Range Count	\N	10
31	2	\N	\N	\N
32	1	Code	Possible values: php, cfm, asp.	php
32	2	\N	\N	\N
34	1	Paths	\N	\N
34	2	\N	\N	\N
33	1	URLs	\N	\N
33	2	\N	\N	\N
35	1	Paths	\N	\N
35	2	\N	\N	\N
36	1	Paths	\N	\N
36	2	\N	\N	\N
37	1	Timeout	\N	10
37	2	\N	\N	\N
38	1	Page Type	Possible values: php, asp.	php
38	2	\N	\N	\N
39	1	Cookie	\N	\N
39	2	\N	\N	\N
40	1	URL Limit	\N	100
40	2	\N	\N	\N
\.


--
-- TOC entry 2092 (class 0 OID 21648)
-- Dependencies: 149
-- Data for Name: check_results; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results (id, check_id, result, sort_order) FROM stdin;
\.


--
-- TOC entry 2093 (class 0 OID 21657)
-- Dependencies: 151
-- Data for Name: check_results_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results_l10n (check_result_id, language_id, result) FROM stdin;
\.


--
-- TOC entry 2094 (class 0 OID 21663)
-- Dependencies: 152
-- Data for Name: check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions (id, check_id, solution, sort_order) FROM stdin;
\.


--
-- TOC entry 2095 (class 0 OID 21672)
-- Dependencies: 154
-- Data for Name: check_solutions_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions_l10n (check_solution_id, language_id, solution) FROM stdin;
\.


--
-- TOC entry 2096 (class 0 OID 21678)
-- Dependencies: 155
-- Data for Name: checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks (id, check_control_id, name, background_info, hints, advanced, automated, script, multiple_solutions, protocol, port, reference, question, reference_id, reference_code, reference_url, effort) FROM stdin;
2	1	DNS A (Non-Recursive)			f	t	dns_a_nr.py	f		\N	\N		1			2
3	1	DNS AFXR			f	t	dns_afxr.pl	f		\N	\N		1			2
4	1	DNS DOM MX			f	t	dns_dom_mx.pl	f		\N	\N		1			2
6	1	DNS Hosting			f	t	dns_hosting.py	f		\N	\N		1			2
7	1	DNS IP Range			f	t	dns_ip_range.pl	f		\N	\N		1			2
9	1	DNS NIC Whois			f	t	nic_whois.pl	f		\N	\N		1			2
10	1	DNS NS Version			f	t	ns_version.pl	f		\N	\N		1			2
12	1	DNS SOA			f	t	dns_soa.py	f		\N	\N		1			2
13	1	DNS SPF			f	t	dns_spf.py	f		\N	\N		1			2
15	1	DNS Subdomain Bruteforce			f	t	subdomain_bruteforce.pl	f		\N	\N		1			2
16	1	DNS Top TLDs			f	t	dns_top_tlds.pl	f		\N	\N		1			2
17	2	FTP Bruteforce			f	t	ftp_bruteforce.pl	f		\N	\N		1			2
18	3	SMTP Banner			f	t	smtp_banner.py	f		\N	\N		1			2
19	3	SMTP DNSBL			f	t	smtp_dnsbl.py	f		\N	\N		1			2
20	3	SMTP Filter			f	t	smtp_filter.py	f		\N	\N		1			2
21	3	SMTP Relay			f	t	smtp_relay.pl	f		\N	\N		1			2
22	4	SSH Bruteforce			f	t	ssh_bruteforce.pl	f		\N	\N		1			2
23	5	Nmap Port Scan			f	t	pscan.pl	f		\N	\N		1			2
24	5	TCP Port Scan			f	t	portscan.pl	f		\N	\N		1			2
25	5	TCP Traceroute			f	t	tcp_traceroute.py	f		80	\N		1			2
26	6	Apache DoS			f	t	apache_dos.pl	f		\N	\N		1			2
27	6	Fuzz Check			f	t	fuzz_check.pl	f		\N	\N		1			2
28	6	Google URL			f	t	google_url.pl	f		\N	\N		1			2
29	6	Grep URL			f	t	grep_url.pl	f	http	\N	\N		1			2
30	6	HTTP Banner			f	t	http_banner.pl	f	http	\N	\N		1			2
31	6	Joomla Scan			f	t	joomla_scan.pl	f	http	\N	\N		1			2
32	6	Login Pages			f	t	login_pages.pl	f	http	\N	\N		1			2
33	6	Nikto			f	t	nikto.pl	f	http	80	\N		1			2
34	6	URL Scan			f	t	urlscan.pl	f	http	\N	\N		1			2
35	6	Web Auth Scanner			f	t	www_auth_scanner.pl	f	http	80	\N		1			2
36	6	Web Directory Scanner			f	t	www_dir_scanner.pl	f	http	80	\N		1			2
37	6	Web File Scanner			f	t	www_file_scanner.pl	f	http	80	\N		1			2
38	6	Web HTTP Methods			f	t	web_http_methods.py	f		\N	\N		1			2
39	6	Web Server CMS			f	t	webserver_cms.pl	f		\N	\N		1			2
40	6	Web Server Error Message			f	t	webserver_error_msg.pl	f		\N	\N		1			2
41	6	Web Server Files			f	t	webserver_files.pl	f		\N	\N		1			2
42	6	Web Server SSL			f	t	webserver_ssl.pl	f		\N	\N		1			2
43	6	Web SQL XSS			f	t	web_sql_xss.py	f		\N	\N		1			2
1	7	DNS A			f	t	dns_a.py	f		\N	\N		1			2
5	7	DNS Find NS			f	t	dns_find_ns.pl	f		\N	\N		1			2
8	8	DNS NIC Typosquatting			f	t	nic_typosquatting.pl	f		\N	\N		1			2
11	8	DNS Resolve IP			f	t	dns_resolve_ip.pl	f		\N	\N		1			2
14	9	DNS SPF (Perl)			f	t	dns_spf.pl	f		\N	\N		1			2
\.


--
-- TOC entry 2097 (class 0 OID 21687)
-- Dependencies: 157
-- Data for Name: checks_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks_l10n (check_id, language_id, name, background_info, hints, reference, question) FROM stdin;
2	1	DNS A (Non-Recursive)	\N	\N	\N	\N
2	2	\N	\N	\N	\N	\N
3	1	DNS AFXR	\N	\N	\N	\N
3	2	\N	\N	\N	\N	\N
4	1	DNS DOM MX	\N	\N	\N	\N
4	2	\N	\N	\N	\N	\N
6	1	DNS Hosting	\N	\N	\N	\N
6	2	\N	\N	\N	\N	\N
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
1	1	DNS A	\N	\N	\N	\N
1	2	\N	\N	\N	\N	\N
5	1	DNS Find NS	\N	\N	\N	\N
5	2	\N	\N	\N	\N	\N
8	1	DNS NIC Typosquatting	\N	\N	\N	\N
8	2	\N	\N	\N	\N	\N
11	1	DNS Resolve IP	\N	\N	\N	\N
11	2	\N	\N	\N	\N	\N
14	1	DNS SPF (Perl)	\N	\N	\N	\N
14	2	\N	\N	\N	\N	\N
\.


--
-- TOC entry 2098 (class 0 OID 21693)
-- Dependencies: 158
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY clients (id, name, country, state, city, address, postcode, website, contact_name, contact_phone, contact_email) FROM stdin;
1	Test									
2	Ziga									
\.


--
-- TOC entry 2099 (class 0 OID 21701)
-- Dependencies: 160
-- Data for Name: emails; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY emails (id, user_id, subject, content, attempts, sent) FROM stdin;
\.


--
-- TOC entry 2100 (class 0 OID 21711)
-- Dependencies: 162
-- Data for Name: languages; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY languages (id, name, code, "default") FROM stdin;
1	English	en	t
2	Deutsch	de	f
\.


--
-- TOC entry 2101 (class 0 OID 21719)
-- Dependencies: 164
-- Data for Name: project_details; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_details (id, project_id, subject, content) FROM stdin;
\.


--
-- TOC entry 2102 (class 0 OID 21727)
-- Dependencies: 166
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY projects (id, client_id, year, deadline, name, status) FROM stdin;
1	1	2012	2012-07-27	Test	in_progress
2	1	2012	2012-07-29	Pipa	open
\.


--
-- TOC entry 2103 (class 0 OID 21736)
-- Dependencies: 168
-- Data for Name: references; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY "references" (id, name, url) FROM stdin;
1	CUSTOM	
\.


--
-- TOC entry 2113 (class 0 OID 22073)
-- Dependencies: 183
-- Data for Name: risk_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_categories (id, name) FROM stdin;
4	Category A
6	Category B
7	Category C
\.


--
-- TOC entry 2114 (class 0 OID 22082)
-- Dependencies: 184
-- Data for Name: risk_categories_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_categories_l10n (risk_category_id, language_id, name) FROM stdin;
4	1	Category A
4	2	\N
6	1	Category B
6	2	\N
7	1	Category C
7	2	\N
\.


--
-- TOC entry 2104 (class 0 OID 21744)
-- Dependencies: 170
-- Data for Name: system; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY system (id, backup) FROM stdin;
1	2012-07-27 02:02:38.61638
\.


--
-- TOC entry 2105 (class 0 OID 21749)
-- Dependencies: 172
-- Data for Name: target_check_attachments; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_attachments (target_id, check_id, name, type, path, size) FROM stdin;
\.


--
-- TOC entry 2106 (class 0 OID 21756)
-- Dependencies: 173
-- Data for Name: target_check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_categories (target_id, check_category_id, advanced, check_count, finished_count, low_risk_count, med_risk_count, high_risk_count) FROM stdin;
1	1	t	16	9	3	3	1
2	2	t	1	1	0	1	0
2	3	t	4	4	0	2	0
2	6	t	18	3	0	1	2
\.


--
-- TOC entry 2107 (class 0 OID 21764)
-- Dependencies: 174
-- Data for Name: target_check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_inputs (target_id, check_input_id, value, file, check_id) FROM stdin;
1	2	\N	\N	2
1	3	120	\N	4
1	4	1	\N	4
1	7	0	\N	6
1	8	10	\N	8
1	9	100	\N	8
1	10	1	\N	8
1	1	\N	\N	1
1	5	120	\N	5
1	6	1	\N	5
2	15	\N	\N	17
2	16	\N	\N	17
2	17	\N	\N	20
2	18	\N	\N	20
2	19	\N	\N	20
2	20	\N	\N	20
2	21	\N	\N	20
2	22	\N	\N	20
2	23	10	\N	21
2	24	source@gmail.com	\N	21
2	25	destination@gmail.com	\N	21
\.


--
-- TOC entry 2108 (class 0 OID 21770)
-- Dependencies: 175
-- Data for Name: target_check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_solutions (target_id, check_solution_id, check_id) FROM stdin;
\.


--
-- TOC entry 2109 (class 0 OID 21773)
-- Dependencies: 176
-- Data for Name: target_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_checks (target_id, check_id, result, target_file, rating, started, pid, status, result_file, language_id, protocol, port, override_target, user_id) FROM stdin;
1	3	\N	\N	high_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	2	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	4	\N	\N	low_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	6	\N	\N	hidden	\N	\N	finished	\N	1	\N	\N	\N	1
1	8	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	11	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	14	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1
1	1	\N	\N	low_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	5	\N	\N	low_risk	\N	\N	finished	\N	1	\N	\N	\N	1
2	17	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1
2	18	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1
2	19	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1
2	20	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1
2	21	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1
2	29	\N	\N	high_risk	\N	\N	finished	\N	1	http	\N	\N	1
2	30	\N	\N	med_risk	\N	\N	finished	\N	1	http	\N	\N	1
2	31	\N	\N	high_risk	\N	\N	finished	\N	1	http	\N	\N	1
\.


--
-- TOC entry 2110 (class 0 OID 21780)
-- Dependencies: 177
-- Data for Name: target_references; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_references (target_id, reference_id) FROM stdin;
1	1
2	1
3	1
\.


--
-- TOC entry 2111 (class 0 OID 21783)
-- Dependencies: 178
-- Data for Name: targets; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY targets (id, project_id, host) FROM stdin;
1	1	google.com
2	1	test.com
3	1	empty.com
\.


--
-- TOC entry 2112 (class 0 OID 21791)
-- Dependencies: 180
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY users (id, email, password, name, client_id, role) FROM stdin;
1	oliver@muenchow.ch	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3	Oliver Muenchow	\N	admin
\.


--
-- TOC entry 1984 (class 2606 OID 21816)
-- Dependencies: 142 142 142
-- Name: check_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_pkey PRIMARY KEY (check_category_id, language_id);


--
-- TOC entry 1982 (class 2606 OID 21818)
-- Dependencies: 140 140
-- Name: check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories
    ADD CONSTRAINT check_categories_pkey PRIMARY KEY (id);


--
-- TOC entry 1988 (class 2606 OID 21820)
-- Dependencies: 145 145 145
-- Name: check_controls_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_pkey PRIMARY KEY (check_control_id, language_id);


--
-- TOC entry 1986 (class 2606 OID 21822)
-- Dependencies: 143 143
-- Name: check_controls_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_controls
    ADD CONSTRAINT check_controls_pkey PRIMARY KEY (id);


--
-- TOC entry 1992 (class 2606 OID 21824)
-- Dependencies: 148 148 148
-- Name: check_inputs_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_pkey PRIMARY KEY (check_input_id, language_id);


--
-- TOC entry 1990 (class 2606 OID 21826)
-- Dependencies: 146 146
-- Name: check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_pkey PRIMARY KEY (id);


--
-- TOC entry 1996 (class 2606 OID 21828)
-- Dependencies: 151 151 151
-- Name: check_results_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_pkey PRIMARY KEY (check_result_id, language_id);


--
-- TOC entry 1994 (class 2606 OID 21830)
-- Dependencies: 149 149
-- Name: check_results_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_pkey PRIMARY KEY (id);


--
-- TOC entry 2000 (class 2606 OID 21832)
-- Dependencies: 154 154 154
-- Name: check_solutions_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_pkey PRIMARY KEY (check_solution_id, language_id);


--
-- TOC entry 1998 (class 2606 OID 21834)
-- Dependencies: 152 152
-- Name: check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_pkey PRIMARY KEY (id);


--
-- TOC entry 2004 (class 2606 OID 21836)
-- Dependencies: 157 157 157
-- Name: checks_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_pkey PRIMARY KEY (check_id, language_id);


--
-- TOC entry 2002 (class 2606 OID 21838)
-- Dependencies: 155 155
-- Name: checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_pkey PRIMARY KEY (id);


--
-- TOC entry 2006 (class 2606 OID 21840)
-- Dependencies: 158 158
-- Name: clients_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- TOC entry 2008 (class 2606 OID 21842)
-- Dependencies: 160 160
-- Name: emails_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_pkey PRIMARY KEY (id);


--
-- TOC entry 2010 (class 2606 OID 21844)
-- Dependencies: 162 162
-- Name: languages_code_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_code_key UNIQUE (code);


--
-- TOC entry 2012 (class 2606 OID 21846)
-- Dependencies: 162 162
-- Name: languages_name_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_name_key UNIQUE (name);


--
-- TOC entry 2014 (class 2606 OID 21848)
-- Dependencies: 162 162
-- Name: languages_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- TOC entry 2016 (class 2606 OID 21850)
-- Dependencies: 164 164
-- Name: project_details_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_pkey PRIMARY KEY (id);


--
-- TOC entry 2018 (class 2606 OID 21852)
-- Dependencies: 166 166
-- Name: projects_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- TOC entry 2020 (class 2606 OID 21854)
-- Dependencies: 168 168
-- Name: references_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY "references"
    ADD CONSTRAINT references_pkey PRIMARY KEY (id);


--
-- TOC entry 2044 (class 2606 OID 22089)
-- Dependencies: 184 184 184
-- Name: risk_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_pkey PRIMARY KEY (risk_category_id, language_id);


--
-- TOC entry 2042 (class 2606 OID 22081)
-- Dependencies: 183 183
-- Name: risk_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_categories
    ADD CONSTRAINT risk_categories_pkey PRIMARY KEY (id);


--
-- TOC entry 2022 (class 2606 OID 21856)
-- Dependencies: 170 170
-- Name: system_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY system
    ADD CONSTRAINT system_pkey PRIMARY KEY (id);


--
-- TOC entry 2024 (class 2606 OID 21858)
-- Dependencies: 172 172
-- Name: target_check_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_pkey PRIMARY KEY (path);


--
-- TOC entry 2026 (class 2606 OID 21860)
-- Dependencies: 173 173 173
-- Name: target_check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_pkey PRIMARY KEY (target_id, check_category_id);


--
-- TOC entry 2028 (class 2606 OID 21862)
-- Dependencies: 174 174 174
-- Name: target_check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_pkey PRIMARY KEY (target_id, check_input_id);


--
-- TOC entry 2030 (class 2606 OID 21864)
-- Dependencies: 175 175 175
-- Name: target_check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_pkey PRIMARY KEY (target_id, check_solution_id);


--
-- TOC entry 2032 (class 2606 OID 21866)
-- Dependencies: 176 176 176
-- Name: target_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_pkey PRIMARY KEY (target_id, check_id);


--
-- TOC entry 2034 (class 2606 OID 21868)
-- Dependencies: 177 177 177
-- Name: target_references_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_pkey PRIMARY KEY (target_id, reference_id);


--
-- TOC entry 2036 (class 2606 OID 21870)
-- Dependencies: 178 178
-- Name: targets_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_pkey PRIMARY KEY (id);


--
-- TOC entry 2038 (class 2606 OID 21872)
-- Dependencies: 180 180
-- Name: users_email_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 2040 (class 2606 OID 21874)
-- Dependencies: 180 180
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 2045 (class 2606 OID 21875)
-- Dependencies: 142 140 1981
-- Name: check_categories_l10n_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2046 (class 2606 OID 21880)
-- Dependencies: 142 162 2013
-- Name: check_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2047 (class 2606 OID 21885)
-- Dependencies: 143 140 1981
-- Name: check_controls_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls
    ADD CONSTRAINT check_controls_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2048 (class 2606 OID 21890)
-- Dependencies: 145 143 1985
-- Name: check_controls_l10n_check_control_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_check_control_id_fkey FOREIGN KEY (check_control_id) REFERENCES check_controls(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2049 (class 2606 OID 21895)
-- Dependencies: 145 162 2013
-- Name: check_controls_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2050 (class 2606 OID 21900)
-- Dependencies: 146 155 2001
-- Name: check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2051 (class 2606 OID 21905)
-- Dependencies: 148 146 1989
-- Name: check_inputs_l10n_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2052 (class 2606 OID 21910)
-- Dependencies: 148 162 2013
-- Name: check_inputs_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2053 (class 2606 OID 21915)
-- Dependencies: 155 149 2001
-- Name: check_results_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2054 (class 2606 OID 21920)
-- Dependencies: 151 1993 149
-- Name: check_results_l10n_check_result_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_check_result_id_fkey FOREIGN KEY (check_result_id) REFERENCES check_results(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2055 (class 2606 OID 21925)
-- Dependencies: 151 162 2013
-- Name: check_results_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2056 (class 2606 OID 21930)
-- Dependencies: 155 2001 152
-- Name: check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2057 (class 2606 OID 21935)
-- Dependencies: 1997 154 152
-- Name: check_solutions_l10n_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2058 (class 2606 OID 21940)
-- Dependencies: 154 2013 162
-- Name: check_solutions_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2059 (class 2606 OID 21945)
-- Dependencies: 1985 155 143
-- Name: checks_check_control_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_check_control_id_fkey FOREIGN KEY (check_control_id) REFERENCES check_controls(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2061 (class 2606 OID 21950)
-- Dependencies: 157 155 2001
-- Name: checks_l10n_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2062 (class 2606 OID 21955)
-- Dependencies: 2013 157 162
-- Name: checks_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2060 (class 2606 OID 21960)
-- Dependencies: 155 168 2019
-- Name: checks_reference_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_reference_id_fkey FOREIGN KEY (reference_id) REFERENCES "references"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2063 (class 2606 OID 21965)
-- Dependencies: 180 2039 160
-- Name: emails_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2064 (class 2606 OID 21970)
-- Dependencies: 166 164 2017
-- Name: project_details_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2065 (class 2606 OID 21975)
-- Dependencies: 158 2005 166
-- Name: projects_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2085 (class 2606 OID 22095)
-- Dependencies: 184 2013 162
-- Name: risk_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2084 (class 2606 OID 22090)
-- Dependencies: 2041 184 183
-- Name: risk_categories_l10n_risk_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_risk_category_id_fkey FOREIGN KEY (risk_category_id) REFERENCES risk_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2066 (class 2606 OID 21980)
-- Dependencies: 2001 172 155
-- Name: target_check_attachments_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2067 (class 2606 OID 21985)
-- Dependencies: 178 172 2035
-- Name: target_check_attachments_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2068 (class 2606 OID 21990)
-- Dependencies: 1981 140 173
-- Name: target_check_categories_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2069 (class 2606 OID 21995)
-- Dependencies: 178 2035 173
-- Name: target_check_categories_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2070 (class 2606 OID 22000)
-- Dependencies: 2001 174 155
-- Name: target_check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2071 (class 2606 OID 22005)
-- Dependencies: 1989 174 146
-- Name: target_check_inputs_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2072 (class 2606 OID 22010)
-- Dependencies: 174 178 2035
-- Name: target_check_inputs_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2073 (class 2606 OID 22015)
-- Dependencies: 175 155 2001
-- Name: target_check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id);


--
-- TOC entry 2074 (class 2606 OID 22020)
-- Dependencies: 1997 175 152
-- Name: target_check_solutions_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2075 (class 2606 OID 22025)
-- Dependencies: 2035 178 175
-- Name: target_check_solutions_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2076 (class 2606 OID 22030)
-- Dependencies: 2001 155 176
-- Name: target_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2077 (class 2606 OID 22035)
-- Dependencies: 2013 162 176
-- Name: target_checks_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2078 (class 2606 OID 22040)
-- Dependencies: 2035 178 176
-- Name: target_checks_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2079 (class 2606 OID 22045)
-- Dependencies: 180 176 2039
-- Name: target_checks_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2080 (class 2606 OID 22050)
-- Dependencies: 177 168 2019
-- Name: target_references_reference_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_reference_id_fkey FOREIGN KEY (reference_id) REFERENCES "references"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2081 (class 2606 OID 22055)
-- Dependencies: 177 2035 178
-- Name: target_references_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2082 (class 2606 OID 22060)
-- Dependencies: 2017 178 166
-- Name: targets_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2083 (class 2606 OID 22065)
-- Dependencies: 180 158 2005
-- Name: users_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2119 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2012-07-31 15:54:23 MSK

--
-- PostgreSQL database dump complete
--

