--
-- PostgreSQL database dump
--

SET client_encoding = 'SQL_ASCII';
SET check_function_bodies = false;

SET search_path = public, pg_catalog;

--
-- TOC entry 3 (OID 38608)
-- Name: rdp1; Type: TABLE; Schema: public; Owner: ian
--

CREATE TABLE rdp1 (
    depth integer,
    pressure_group character(1),
    "time" interval hour to minute,
    deco character(1)
) WITHOUT OIDS;


