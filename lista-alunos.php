<?php

use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require_once 'vendor/autoload.php';

$studentRepository = new PdoStudentRepository();
$studentList = $studentRepository->allStudents();
var_dump($studentList);