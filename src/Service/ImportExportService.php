<?php

namespace App\Service;

use App\Entity\BaseProduct;
use App\Entity\Grocery;
use App\Entity\Price;
use App\Enum\GroceryEnum;
use App\Enum\ShopEnum;
use App\Enum\UnitEnum;
use App\Repository\BaseProductRepository;
use App\Repository\GroceryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GroceryRepository $groceryRepository,
        private BaseProductRepository $baseProductRepository,
    ) {}

    /**
     * Export all groceries to CSV format
     */
    public function exportGroceries(): StreamedResponse
    {
        $response = new StreamedResponse();
        $response->setCallback(function () {
            $handle = fopen('php://output', 'w+');
            
            // Write CSV header
            fputcsv($handle, ['Name', 'Type', 'Unit', 'Base Product']);
            
            // Get all groceries
            $groceries = $this->groceryRepository->findAll();
            
            foreach ($groceries as $grocery) {
                fputcsv($handle, [
                    $grocery->getName(),
                    $grocery->getType()->value,
                    $grocery->getUnit()->value,
                    $grocery->getBaseProduct()?->getName() ?? '',
                ]);
            }
            
            fclose($handle);
        });
        
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="groceries_' . date('Y-m-d_His') . '.csv"');
        
        return $response;
    }

    /**
     * Export all prices to CSV format
     */
    public function exportPrices(): StreamedResponse
    {
        $response = new StreamedResponse();
        $response->setCallback(function () {
            $handle = fopen('php://output', 'w+');
            
            // Write CSV header
            fputcsv($handle, ['Grocery Name', 'Shop', 'Value', 'Date']);
            
            // Get all prices
            $prices = $this->entityManager->getRepository(Price::class)->findAll();
            
            foreach ($prices as $price) {
                fputcsv($handle, [
                    $price->getGrocery()->getName(),
                    $price->getShop()->value,
                    $price->getValue(),
                    $price->getCreatedAt()->format('Y-m-d'),
                ]);
            }
            
            fclose($handle);
        });
        
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="prices_' . date('Y-m-d_His') . '.csv"');
        
        return $response;
    }

    /**
     * Import groceries from CSV file
     * 
     * @param resource $file
     * @return array{success: int, failed: int, errors: string[]}
     */
    public function importGroceries($file): array
    {
        $result = ['success' => 0, 'failed' => 0, 'errors' => []];
        
        if (feof($file)) {
            rewind($file);
        }
        
        // Skip header
        fgetcsv($file);
        
        $row = 0;
        while (($data = fgetcsv($file)) !== false) {
            $row++;
            
            if (empty($data[0])) {
                continue;
            }
            
            try {
                $name = $data[0];
                $type = $data[1] ?? null;
                $unit = $data[2] ?? null;
                $baseProductName = $data[3] ?? null;
                
                // Validate enum values
                try {
                    $typeEnum = GroceryEnum::from($type);
                } catch (\ValueError) {
                    $result['errors'][] = "Row $row: Invalid type '$type'";
                    $result['failed']++;
                    continue;
                }
                
                try {
                    $unitEnum = UnitEnum::from($unit);
                } catch (\ValueError) {
                    $result['errors'][] = "Row $row: Invalid unit '$unit'";
                    $result['failed']++;
                    continue;
                }
                
                // Check if grocery already exists
                $existing = $this->groceryRepository->findOneBy(['name' => $name]);
                if ($existing) {
                    $result['errors'][] = "Row $row: Grocery '$name' already exists";
                    $result['failed']++;
                    continue;
                }
                
                // Find base product if specified
                $baseProduct = null;
                if (!empty($baseProductName)) {
                    $baseProduct = $this->baseProductRepository->findOneBy(['name' => $baseProductName]);
                    if (!$baseProduct) {
                        $result['errors'][] = "Row $row: Base Product '$baseProductName' not found";
                        $result['failed']++;
                        continue;
                    }
                }
                
                // Create and persist grocery
                $grocery = new Grocery();
                $grocery->setName($name);
                $grocery->setType($typeEnum);
                $grocery->setUnit($unitEnum);
                if ($baseProduct) {
                    $grocery->setBaseProduct($baseProduct);
                }
                
                $this->entityManager->persist($grocery);
                $result['success']++;
            } catch (\Exception $e) {
                $result['errors'][] = "Row $row: " . $e->getMessage();
                $result['failed']++;
            }
        }
        
        if ($result['success'] > 0) {
            $this->entityManager->flush();
        }
        
        return $result;
    }

    /**
     * Import prices from CSV file
     * 
     * @param resource $file
     * @return array{success: int, failed: int, errors: string[]}
     */
    public function importPrices($file): array
    {
        $result = ['success' => 0, 'failed' => 0, 'errors' => []];
        
        if (feof($file)) {
            rewind($file);
        }
        
        // Skip header
        fgetcsv($file);
        
        $row = 0;
        while (($data = fgetcsv($file)) !== false) {
            $row++;
            
            if (empty($data[0])) {
                continue;
            }
            
            try {
                [$groceryName, $shop, $value, $date] = $data;
                
                // Find grocery
                $grocery = $this->groceryRepository->findOneBy(['name' => $groceryName]);
                if (!$grocery) {
                    $result['errors'][] = "Row $row: Grocery '$groceryName' not found";
                    $result['failed']++;
                    continue;
                }
                
                // Validate enum values
                try {
                    $shopEnum = ShopEnum::from($shop);
                } catch (\ValueError) {
                    $result['errors'][] = "Row $row: Invalid shop '$shop'";
                    $result['failed']++;
                    continue;
                }
                
                // Validate and parse date
                try {
                    $dateTime = \DateTimeImmutable::createFromFormat('Y-m-d', $date);
                    if (!$dateTime) {
                        throw new \Exception("Invalid date format");
                    }
                } catch (\Exception $e) {
                    $result['errors'][] = "Row $row: Invalid date format '$date' (expected Y-m-d)";
                    $result['failed']++;
                    continue;
                }
                
                // Validate price value
                if (!is_numeric($value) || $value <= 0) {
                    $result['errors'][] = "Row $row: Invalid price value '$value'";
                    $result['failed']++;
                    continue;
                }
                
                // Create and persist price
                $price = new Price();
                $price->setGrocery($grocery);
                $price->setShop($shopEnum);
                $price->setValue((string)$value);
                $price->setCreatedAt($dateTime);
                
                $this->entityManager->persist($price);
                $result['success']++;
            } catch (\Exception $e) {
                $result['errors'][] = "Row $row: " . $e->getMessage();
                $result['failed']++;
            }
        }
        
        if ($result['success'] > 0) {
            $this->entityManager->flush();
        }
        
        return $result;
    }

    /**
     * Get valid enum values for CSV reference
     */
    public function getValidEnumValues(): array
    {
        return [
            'types' => array_map(fn($case) => $case->value, GroceryEnum::cases()),
            'units' => array_map(fn($case) => $case->value, UnitEnum::cases()),
            'shops' => array_map(fn($case) => $case->value, ShopEnum::cases()),
        ];
    }
}
