<?php
namespace Source\Controllers;

use Dotenv\Repository\RepositoryInterface;
use Exception;
use League\Csv\Writer;
use Source\Expections\FileException;
use Source\Expections\TransactionException;
use Source\Models\Transaction;
use Source\Support\Response;
use SplTempFileObject;

use function PHPSTORM_META\type;

class FileController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function exportByTypeToCsv(array $data): void
    {
        try {
            $transaction = new Transaction();
            $transactions = $transaction->getByType($data['type']);
            if (empty($transactions)) {
                Response::success("Usuário não possui transações no sistema", response: []);
                return;
            }

            $this->makeCsvFile($transactions);
        } catch (FileException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch (TransactionException $e) {
            Response::error($e->getMessage(), $e->getCode(), $e->getErrors());
        } catch (Exception $e) {
            Response::serverError();
        }
    }

    private function makeCsvFile(array $transactions): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="dados.csv"');

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->insertOne(Transaction::$headCsvFile);

        foreach($transactions['transactions'] as $transaction) {
            $category = $transaction->category->name ?? null;
            $type = $transaction->type;
            $amount = $transaction->amount;
            $description = $transaction->description;
            $date = convertDateUsToBr($transaction->created_at);

            $csv->insertOne([$category, $type, $amount, $description, $date]);
        }

        $csv->output('dados.csv');
    }
}