<?php

namespace Alura\Pdo\Infrastructure\Repository;

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Domain\Repository\StudentRepository;
use DateTimeInterface;
use PDO;
use PDOStatement;

class PdoStudentRepository implements StudentRepository
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function allStudents(): array
    {
        $stmt = $this->connection->query('SELECT * FROM students;');
        return $this->hydrateStudentList($stmt);
    }

    /**
     * @throws \Exception
     */
    public function studentBirthAt(DateTimeInterface $birthDate): array
    {
        $stmt = $this->connection->prepare('SELECT * FROM students WHERE birth_date = :birth_date;');
        $stmt->bindValue(':birth_date', $birthDate->format('Y-m-d'));
        $stmt->execute();

        return $this->hydrateStudentList($stmt);
    }

    private function hydrateStudentList(PDOStatement $stmt): array
    {
        $studentDataList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($studentDataList as $studentData) {
            $studentList[] = new Student(
                $studentData['id'],
                $studentData['name'],
                new \DateTimeImmutable($studentData['birth_date'])
            );
        }

        return $studentDataList;
    }

    public function save(Student $student): bool
    {
        if ($student->id() === null) {
            return $this->insert($student);
        }

        return $this->update($student);
    }

    private function insert(Student $student): bool
    {
        $insertQuery = 'INSERT INTO students (name, birth_date) VALUES (:name, :birth_date);';
        $stmt = $this->connection->prepare($insertQuery);

        $success = $stmt->execute([
            ':name' => $student->name(),
            ':birth_date' => $student->birthDate()->format('Y-m-d')
        ]);

        if ($success) {
            $student->defineId($this->connection->lastInsertId());
        }

        return $success;
    }

    private function update(Student $student): bool
    {
        $insertQuery = 'UPDATE students SET name = :name, birth_date = :birth_date WHERE id = :id;';
        $statement = $this->connection->prepare($insertQuery);
        $statement->bindValue(':name', $student->name());
        $statement->bindValue(':birth_date', $student->birthDate()->format('Y-m-d'));
        $statement->bindValue(':name', $student->id(), PDO::PARAM_INT);

        return $statement->execute();
    }

    public function remove(Student $student): bool
    {
        $preparedStatement = $this->connection->prepare('DELETE FROM students WHERE id = ?;');
        $preparedStatement->bindValue(1, $student->id(), PDO::PARAM_INT);
        return $preparedStatement->execute();
    }
}