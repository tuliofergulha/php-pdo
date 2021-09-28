<?php

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;

require_once 'vendor/autoload.php';

$student = new Student(
    null,
    "Teste 5",
    new DateTimeImmutable('1997-08-03'));

$studentRepository = new PdoStudentRepository();
echo $studentRepository->save($student);