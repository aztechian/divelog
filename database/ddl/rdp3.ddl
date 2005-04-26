--
-- PostgreSQL database dump
--

SET client_encoding = 'SQL_ASCII';
SET check_function_bodies = false;

SET search_path = public, pg_catalog;

--
-- TOC entry 3 (OID 38623)
-- Name: rdp3; Type: TABLE; Schema: public; Owner: ian
--

CREATE TABLE rdp3 (
    pressure_group character(1),
    depth integer,
    residual interval hour to minute,
    max_bt interval hour to minute
) WITHOUT OIDS;


