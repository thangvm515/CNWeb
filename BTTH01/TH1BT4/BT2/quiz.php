<?php
// quiz.php - Lấy dữ liệu từ CSDL, hiển thị bài thi và tính điểm

require 'config.php';


function arrays_are_equal($a, $b) {
    if (count($a) !== count($b)) {
        return false; 
    }
    
    sort($a);
    sort($b);
    return $a === $b; 
}

$quizData = [];
$db_error = null;
try {
    $stmt_q = $pdo->query("SELECT id, question_text, question_type FROM questions ORDER BY id ASC");
    $questions = $stmt_q->fetchAll();

    $stmt_o = $pdo->query("SELECT question_id, option_key, option_text, is_correct FROM options ORDER BY question_id, option_key ASC");
    $options_raw = $stmt_o->fetchAll();

    $options_map = [];
    foreach ($options_raw as $opt) {
        $options_map[$opt['question_id']][] = $opt;
    }

    foreach ($questions as $q) {
        $question_id = $q['id'];
        $options_list = [];
        $answer_keys = [];
        
        if (isset($options_map[$question_id])) {
            foreach ($options_map[$question_id] as $opt) {
                $options_list[$opt['option_key']] = $opt['option_text'];
                if ($opt['is_correct']) {
                    $answer_keys[] = $opt['option_key'];
                }
            }
        }
        
        if (empty($options_list) || empty($answer_keys)) continue;

        $quizData[] = [
            'id' => $question_id,
            'question_text' => $q['question_text'],
            'options' => $options_list,
            'question_type' => $q['question_type'],
            'answer_keys' => $answer_keys 
        ];
    }
} catch (Exception $e) {
    $db_error = "Lỗi truy vấn CSDL. Vui lòng kiểm tra CSDL và dữ liệu.";
}


$score = 0;
$showResults = false;
$userAnswers = [];
$totalQuestions = count($quizData);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $showResults = true;
    
    foreach ($quizData as $qItem) {
        $qId = $qItem['id'];
        $form_name = "q{$qId}"; 
        $userAnswer = isset($_POST[$form_name]) ? $_POST[$form_name] : null;
        
        if ($qItem['question_type'] === 'multiple' && is_array($userAnswer)) {
            $userAnswers[$qId] = $userAnswer;
        } elseif ($qItem['question_type'] === 'single' && is_string($userAnswer)) {
            $userAnswers[$qId] = $userAnswer;
        } else {
            $userAnswers[$qId] = null;
        }

        $isCorrect = false;
        
        if ($qItem['question_type'] === 'multiple' && is_array($userAnswers[$qId])) {
            $isCorrect = arrays_are_equal($userAnswers[$qId], $qItem['answer_keys']);
            
        } elseif ($qItem['question_type'] === 'single' && $userAnswers[$qId] === $qItem['answer_keys'][0]) {
            $isCorrect = true;
        }

        if ($isCorrect) {
            $score++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài Thi Trắc Nghiệm Android (CSDL)</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <div class="container">
        <h1>📚 Bài Thi Trắc Nghiệm Lập trình Android (Dữ liệu từ CSDL)</h1>
        
        <?php if ($db_error): ?>
            <div class="message error">❌ Lỗi CSDL: <?php echo $db_error; ?></div>
        <?php endif; ?>
        
        <?php 
        if ($showResults) {
            echo "<div class='score-display'>Bạn đã trả lời đúng: <strong>$score / $totalQuestions</strong> câu.</div>";
        }
        ?>
        
        <?php if ($totalQuestions === 0): ?>
            <div class="message error">Chưa có câu hỏi nào được tải lên. Vui lòng truy cập <a href="upload.php">Trang Tải Lên Dữ Liệu</a>.</div>
        <?php endif; ?>

        <form method="POST" action="quiz.php">
        
            <?php 
            foreach ($quizData as $qIndex => $qItem) {
                $qNumber = $qIndex + 1;
                $qId = $qItem['id'];
                
                $inputType = ($qItem['question_type'] == 'multiple') ? 'checkbox' : 'radio';
                $inputName = ($qItem['question_type'] == 'multiple') ? "q{$qId}[]" : "q{$qId}";

                $required = ($qItem['question_type'] == 'single' && !$showResults) ? 'required' : '';
                
                $disabled = $showResults ? 'disabled' : '';

                $userAnswer = isset($userAnswers[$qId]) ? $userAnswers[$qId] : null;
                $isCorrectAnswered = false;
                
                if ($showResults) {
                    if ($qItem['question_type'] === 'multiple') {
                        $isCorrectAnswered = is_array($userAnswer) ? arrays_are_equal($userAnswer, $qItem['answer_keys']) : false;
                    } else {
                        $isCorrectAnswered = ($userAnswer === $qItem['answer_keys'][0]);
                    }
                }

                echo "<div class='question-card'>";
                echo "<h3>Câu $qNumber: " . $qItem['question_text'] . "</h3>";

                foreach ($qItem['options'] as $key => $value) {
                    $optionId = "q" . $qId . "_$key";
                    
                    if ($qItem['question_type'] === 'multiple') {
                        $checked = (is_array($userAnswer) && in_array($key, $userAnswer)) ? 'checked' : '';
                    } else {
                        $checked = ($userAnswer === $key) ? 'checked' : '';
                    }
                    
                    $class = '';
                    if ($showResults) {
                        $isUserSelected = (is_array($userAnswer) && in_array($key, $userAnswer)) || ($userAnswer === $key);

                        if (in_array($key, $qItem['answer_keys'])) {
                             $class = 'selected-correct';
                        } elseif ($isUserSelected && !in_array($key, $qItem['answer_keys'])) {
                            $class = 'selected-incorrect';
                        }
                    }
                    
                    echo "<label for='$optionId' class='option $class'>";
                    echo "<input type='$inputType' name='$inputName' id='$optionId' value='$key' $checked $required $disabled>"; 
                    echo "<strong>$key.</strong> $value";
                    echo "</label>";
                }

                if ($showResults && !$isCorrectAnswered) {
                    $correctAnswersText = implode(', ', $qItem['answer_keys']);
                    echo "<p>Đáp án đúng là: <span class='correct-answer-text'><strong>" . $correctAnswersText . "</strong></span></p>";
                }
                
                echo "</div>";
            }
            ?>
            
            <?php if ($totalQuestions > 0): ?>
                <?php if (!$showResults): ?>
                    <button type="submit" class="result-button">Kiểm Tra Đáp Án</button>
                <?php else: ?>
                    <button type="submit" class="result-button" formaction="quiz.php" formmethod="GET">Làm Lại Bài Thi</button>
                <?php endif; ?>
            <?php endif; ?>

        </form>

    </div>
</body>
</html>