<?php

namespace App\Controller;

use App\Service\ImportExportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/import-export')]
final class ImportExportController extends AbstractController
{
    public function __construct(
        private ImportExportService $importExportService,
    )
    {
    }

    #[Route('', name: 'app_import_export', methods: ['GET'])]
    public function index(): Response
    {
        $enumValues = $this->importExportService->getValidEnumValues();

        return $this->render('import_export/index.html.twig', [
            'enumValues' => $enumValues,
        ]);
    }

    #[Route('/export/groceries', name: 'app_export_groceries', methods: ['GET'])]
    public function exportGroceries(): Response
    {
        return $this->importExportService->exportGroceries();
    }

    #[Route('/export/prices', name: 'app_export_prices', methods: ['GET'])]
    public function exportPrices(): Response
    {
        return $this->importExportService->exportPrices();
    }

    #[Route('/import/groceries', name: 'app_import_groceries', methods: ['POST'])]
    public function importGroceries(Request $request): Response
    {
        $file = $request->files->get('groceries_file');

        if (!$file) {
            $this->addFlash('error', 'No file selected');
            return $this->redirectToRoute('app_import_export');
        }

        $handle = fopen($file->getPathname(), 'r');
        $result = $this->importExportService->importGroceries($handle);
        fclose($handle);

        if ($result['success'] > 0) {
            $this->addFlash('success', sprintf(
                'Successfully imported %d groceries',
                $result['success']
            ));
        }

        if (!empty($result['errors'])) {
            foreach ($result['errors'] as $error) {
                $this->addFlash('warning', $error);
            }
        }

        if ($result['failed'] > 0 && $result['success'] === 0) {
            $this->addFlash('error', sprintf(
                'Failed to import %d rows',
                $result['failed']
            ));
        }

        return $this->redirectToRoute('app_import_export');
    }

    #[Route('/import/prices', name: 'app_import_prices', methods: ['POST'])]
    public function importPrices(Request $request): Response
    {
        $file = $request->files->get('prices_file');

        if (!$file) {
            $this->addFlash('error', 'No file selected');
            return $this->redirectToRoute('app_import_export');
        }

        $handle = fopen($file->getPathname(), 'r');
        $result = $this->importExportService->importPrices($handle);
        fclose($handle);

        if ($result['success'] > 0) {
            $this->addFlash('success', sprintf(
                'Successfully imported %d prices',
                $result['success']
            ));
        }

        if (!empty($result['errors'])) {
            foreach ($result['errors'] as $error) {
                $this->addFlash('warning', $error);
            }
        }

        if ($result['failed'] > 0 && $result['success'] === 0) {
            $this->addFlash('error', sprintf(
                'Failed to import %d rows',
                $result['failed']
            ));
        }

        return $this->redirectToRoute('app_import_export');
    }

    #[Route('/export/recipes', name: 'app_export_recipes', methods: ['GET'])]
    public function exportRecipes(): Response
    {
        return $this->importExportService->exportRecipes();
    }

    #[Route('/import/recipes', name: 'app_import_recipes', methods: ['POST'])]
    public function importRecipes(Request $request): Response
    {
        $file = $request->files->get('recipes_file');

        if (!$file) {
            $this->addFlash('error', 'No file selected');
            return $this->redirectToRoute('app_import_export');
        }

        $handle = fopen($file->getPathname(), 'r');
        $result = $this->importExportService->importRecipes($handle);
        fclose($handle);

        if ($result['success'] > 0) {
            $this->addFlash('success', sprintf(
                'Successfully imported %d recipe ingredients',
                $result['success']
            ));
        }

        if (!empty($result['errors'])) {
            foreach ($result['errors'] as $error) {
                $this->addFlash('warning', $error);
            }
        }

        if ($result['failed'] > 0 && $result['success'] === 0) {
            $this->addFlash('error', sprintf(
                'Failed to import %d rows',
                $result['failed']
            ));
        }

        return $this->redirectToRoute('app_import_export');
    }
}
