-- ============================================
-- QUERY UNTUK MENGECEK DATA TRAINING
-- Assignment ID: 18, Session ID: 2
-- ============================================

-- 1. CEK ASSIGNMENT DETAIL
SELECT 
    ta.id as assignment_id,
    ta.training_id,
    ta.employee_id,
    ta.status as assignment_status,
    ta.assigned_date,
    ta.start_date,
    ta.deadline_date,
    tm.training_name,
    tm.training_code,
    tm.status as training_status,
    tm.is_active as training_active
FROM tb_training_assignments ta
LEFT JOIN tb_training_masters tm ON ta.training_id = tm.id
WHERE ta.id = 18;

-- 2. CEK SESSION DETAIL
SELECT 
    ts.id as session_id,
    ts.training_id,
    ts.session_order,
    ts.session_title,
    ts.description,
    ts.difficulty_level_id,
    ts.theme,
    ts.question_count,
    ts.passing_score,
    ts.has_video,
    ts.is_active as session_active,
    tdl.level_name as difficulty_level_name
FROM tb_training_sessions ts
LEFT JOIN tb_training_difficulty_levels tdl ON ts.difficulty_level_id = tdl.id
WHERE ts.id = 2;

-- 3. CEK MATERIAL YANG TERKAIT DENGAN TRAINING MASTER
SELECT 
    tm.id as training_id,
    tm.training_name,
    tmm.material_id,
    tmm.display_order,
    tmat.material_code,
    tmat.material_title,
    tmat.is_active as material_active
FROM tb_training_masters tm
LEFT JOIN tb_training_master_material tmm ON tm.id = tmm.training_id
LEFT JOIN tb_training_materials tmat ON tmm.material_id = tmat.id
WHERE tm.id = (
    SELECT training_id FROM tb_training_assignments WHERE id = 18
)
ORDER BY tmm.display_order;

-- 4. CEK QUESTION BANK YANG SESUAI DENGAN SESSION
-- (Filter berdasarkan material dari training master, difficulty_level, dan theme)
SELECT 
    qb.id as question_id,
    qb.question,
    qb.question_type,
    qb.difficulty_level_id,
    qb.material_id,
    qb.theme,
    qb.correct_answer,
    qb.answer_options,
    qb.score,
    qb.is_active,
    tmat.material_title,
    tdl.level_name as difficulty_level_name
FROM tb_training_question_banks qb
LEFT JOIN tb_training_materials tmat ON qb.material_id = tmat.id
LEFT JOIN tb_training_difficulty_levels tdl ON qb.difficulty_level_id = tdl.id
WHERE qb.material_id IN (
    -- Ambil material_id dari training master
    SELECT tmm.material_id 
    FROM tb_training_master_material tmm
    WHERE tmm.training_id = (
        SELECT training_id FROM tb_training_assignments WHERE id = 18
    )
)
AND qb.difficulty_level_id = (
    -- Ambil difficulty_level_id dari session
    SELECT difficulty_level_id FROM tb_training_sessions WHERE id = 2
)
AND (
    -- Filter theme jika session punya theme
    (SELECT theme FROM tb_training_sessions WHERE id = 2) IS NULL
    OR qb.theme = (SELECT theme FROM tb_training_sessions WHERE id = 2)
)
AND qb.is_active = true
ORDER BY qb.id;

-- 5. HITUNG TOTAL QUESTION BANK YANG TERSEDIA
SELECT 
    COUNT(*) as total_questions_available,
    COUNT(DISTINCT qb.material_id) as materials_with_questions,
    COUNT(DISTINCT qb.difficulty_level_id) as difficulty_levels_available
FROM tb_training_question_banks qb
WHERE qb.material_id IN (
    SELECT tmm.material_id 
    FROM tb_training_master_material tmm
    WHERE tmm.training_id = (
        SELECT training_id FROM tb_training_assignments WHERE id = 18
    )
)
AND qb.difficulty_level_id = (
    SELECT difficulty_level_id FROM tb_training_sessions WHERE id = 2
)
AND (
    (SELECT theme FROM tb_training_sessions WHERE id = 2) IS NULL
    OR qb.theme = (SELECT theme FROM tb_training_sessions WHERE id = 2)
)
AND qb.is_active = true;

-- 6. CEK SESSION PROGRESS (Jika sudah ada)
SELECT 
    tsp.id,
    tsp.assignment_id,
    tsp.session_id,
    tsp.employee_id,
    tsp.status,
    tsp.score,
    tsp.correct_answers_count,
    tsp.total_questions,
    tsp.questions_data,
    tsp.answers_data,
    tsp.started_at,
    tsp.completed_at,
    tsp.attempts_count
FROM tb_training_session_progress tsp
WHERE tsp.assignment_id = 18
AND tsp.session_id = 2;

-- 7. QUERY LENGKAP - SEMUA INFO DALAM SATU QUERY
SELECT 
    -- Assignment Info
    ta.id as assignment_id,
    ta.training_id,
    ta.employee_id,
    ta.status as assignment_status,
    -- Training Info
    tm.training_name,
    tm.training_code,
    tm.status as training_status,
    -- Session Info
    ts.id as session_id,
    ts.session_order,
    ts.session_title,
    ts.difficulty_level_id,
    ts.theme as session_theme,
    ts.question_count as session_question_count,
    ts.passing_score,
    -- Material Count
    (SELECT COUNT(*) FROM tb_training_master_material WHERE training_id = tm.id) as total_materials,
    -- Question Bank Count (Available)
    (
        SELECT COUNT(*) 
        FROM tb_training_question_banks qb
        WHERE qb.material_id IN (
            SELECT material_id FROM tb_training_master_material WHERE training_id = tm.id
        )
        AND qb.difficulty_level_id = ts.difficulty_level_id
        AND (ts.theme IS NULL OR qb.theme = ts.theme)
        AND qb.is_active = true
    ) as available_questions,
    -- Session Progress Info
    tsp.status as progress_status,
    tsp.score as progress_score,
    tsp.total_questions as progress_total_questions,
    CASE 
        WHEN tsp.questions_data IS NOT NULL THEN jsonb_array_length(tsp.questions_data::jsonb)
        ELSE 0
    END as questions_generated
FROM tb_training_assignments ta
LEFT JOIN tb_training_masters tm ON ta.training_id = tm.id
LEFT JOIN tb_training_sessions ts ON ts.training_id = tm.id AND ts.id = 2
LEFT JOIN tb_training_session_progress tsp ON tsp.assignment_id = ta.id AND tsp.session_id = ts.id
WHERE ta.id = 18;

