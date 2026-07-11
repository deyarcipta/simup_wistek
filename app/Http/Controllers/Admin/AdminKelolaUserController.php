<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminKelolaUserController extends Controller
{
    public function index()
    {
        $users = User::whereIn('role', ['admin', 'operator'])
                        ->orderBy('name')
                        ->get();
        return view('admin.kelola_user.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|string'
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'foto'  => '', // Default empty photo
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request,  User $kelola_user)
    {
        // dd($kelola_user->id);
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $kelola_user->id,
            'role'  => 'required|string'
        ]);

        $data = $request->only(['name', 'email', 'role']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $kelola_user->update($data);
        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $kelola_user)
    {
        $kelola_user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }

    public function downloadSample()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Header
        $sheet->setCellValue('A1', 'Nama');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Role');
        $sheet->setCellValue('D1', 'Password (Min 6 Karakter)');
        
        // Style Header
        $styleHeader = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '375623'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '7F7F7F'],
                ],
            ],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($styleHeader);
        
        // Dummy data
        $data = [
            ['Baim Wong', 'baim@gmail.com', 'operator', 'password123'],
            ['Deni Siregar', 'deni@gmail.com', 'operator', 'securepass'],
            ['Administrator Baru', 'adminbaru@gmail.com', 'admin', 'admin123'],
        ];
        
        $rowNum = 2;
        foreach ($data as $row) {
            $sheet->setCellValue('A' . $rowNum, $row[0]);
            $sheet->setCellValue('B' . $rowNum, $row[1]);
            $sheet->setCellValue('C' . $rowNum, $row[2]);
            $sheet->setCellValue('D' . $rowNum, $row[3]);
            $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $rowNum++;
        }
        
        // Style Data Borders
        $styleData = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '7F7F7F'],
                ],
            ],
        ];
        $sheet->getStyle('A2:D' . ($rowNum - 1))->applyFromArray($styleData);
        
        // Auto-width columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="contoh_template_user.xlsx"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        return response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, 200, $headers);
    }

    public function exportExcel()
    {
        $users = User::whereIn('role', ['admin', 'operator'])
                     ->orderBy('name')
                     ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title
        $sheet->setCellValue('A1', 'DAFTAR USER SIMUP WISTEK');
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Dicetak pada: ' . date('d-m-Y H:i'));
        $sheet->mergeCells('A2:E2');
        $sheet->getStyle('A2')->getFont()->setSize(10)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        
        // Table Headers
        $sheet->setCellValue('A4', 'No');
        $sheet->setCellValue('B4', 'Nama');
        $sheet->setCellValue('C4', 'Email');
        $sheet->setCellValue('D4', 'Role');
        $sheet->setCellValue('E4', 'Tanggal Dibuat');
        
        $styleHeader = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A4:E4')->applyFromArray($styleHeader);
        
        // Fill Data
        $rowNum = 5;
        $no = 1;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $user->name);
            $sheet->setCellValue('C' . $rowNum, $user->email);
            $sheet->setCellValue('D' . $rowNum, ucfirst($user->role));
            $sheet->setCellValue('E' . $rowNum, $user->created_at ? $user->created_at->format('d-m-Y H:i') : '-');
            
            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $rowNum++;
        }
        
        // Style Borders
        $styleData = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle('A5:E' . ($rowNum - 1))->applyFromArray($styleData);
        
        // Auto-width columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="daftar_user_' . date('Ymd_His') . '.xlsx"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];
        
        return response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, 200, $headers);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');
        
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            if (count($rows) <= 1) {
                return back()->with('error', 'File Excel kosong atau tidak memiliki data.');
            }

            $importedCount = 0;
            $skippedCount = 0;

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                if (empty($row[0]) && empty($row[1]) && empty($row[3])) {
                    continue;
                }

                $name = trim($row[0] ?? '');
                $email = trim($row[1] ?? '');
                $role = strtolower(trim($row[2] ?? ''));
                $password = trim($row[3] ?? '');

                if (empty($name) || empty($email) || empty($password)) {
                    $skippedCount++;
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skippedCount++;
                    continue;
                }

                $exists = User::where('email', $email)->exists();
                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                if (!in_array($role, ['admin', 'operator'])) {
                    $role = 'operator';
                }

                User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'role' => $role,
                    'foto' => ''
                ]);

                $importedCount++;
            }

            $message = "Proses impor selesai. Berhasil menambahkan {$importedCount} user.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} baris dilewati karena data tidak lengkap, format email salah, atau email sudah terdaftar.";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }
}
