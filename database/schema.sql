-- PTI Student Result Management and Result Checking System
-- PostgreSQL schema

BEGIN;

CREATE TABLE IF NOT EXISTS admins (
    id            BIGSERIAL PRIMARY KEY,
    full_name     VARCHAR(150) NOT NULL,
    username      VARCHAR(60)  NOT NULL UNIQUE,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash TEXT         NOT NULL,
    created_at    TIMESTAMPTZ  NOT NULL DEFAULT now(),
    updated_at    TIMESTAMPTZ  NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS students (
    id            BIGSERIAL PRIMARY KEY,
    matric_number VARCHAR(40)  NOT NULL UNIQUE,
    full_name     VARCHAR(150) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash TEXT         NOT NULL,
    department    VARCHAR(120) NOT NULL,
    programme     VARCHAR(60)  NOT NULL,
    level         VARCHAR(40)  NOT NULL,
    student_photo VARCHAR(255),
    created_at    TIMESTAMPTZ  NOT NULL DEFAULT now(),
    updated_at    TIMESTAMPTZ  NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_students_name ON students (full_name);
CREATE INDEX IF NOT EXISTS idx_students_department ON students (department);

CREATE TABLE IF NOT EXISTS results (
    id               BIGSERIAL PRIMARY KEY,
    student_id       BIGINT       NOT NULL REFERENCES students(id) ON DELETE CASCADE,
    academic_session VARCHAR(20)  NOT NULL,
    semester         VARCHAR(30)  NOT NULL,
    result_pdf       VARCHAR(255) NOT NULL,
    uploaded_at      TIMESTAMPTZ  NOT NULL DEFAULT now(),
    updated_at       TIMESTAMPTZ  NOT NULL DEFAULT now(),
    CONSTRAINT uq_result_student_session_semester UNIQUE (student_id, academic_session, semester)
);

CREATE INDEX IF NOT EXISTS idx_results_student ON results (student_id);
CREATE INDEX IF NOT EXISTS idx_results_session ON results (academic_session);
CREATE INDEX IF NOT EXISTS idx_results_semester ON results (semester);

COMMIT;
