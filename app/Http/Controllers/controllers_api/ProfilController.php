<?php

namespace App\Http\Controllers\controllers_api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\User;
use App\Models\Theme;
use App\Models\Durasi;
use App\Models\Marquee;
use App\Models\Petugas;
use App\Models\Profil;
use App\Models\Slides;
use App\Models\Adzan;
use App\Models\AdzanAudio;
use App\Models\Audios;
use App\Models\NewSlider;

class ProfilController extends Controller
{

    public function __construct() {}

    // GET PROFIL
    public function get_profil($slug)
    {
        try {
            // trigger
            $profil = Profil::where('slug', $slug)->firstOrFail();
            $profil->makeVisible(['logo_masjid_url', 'logo_pemerintah_url']);
            $data = [
                'name' => $profil->name,
                'address' => $profil->address,
                'logo_masjid' => $profil->logo_masjid,
                'logo_pemerintah' => $profil->logo_pemerintah,
                'logo_masjid_url' => $profil->logo_masjid_url,
                'logo_pemerintah_url' => $profil->logo_pemerintah_url
            ];

            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data profil masjid !',
                'data' => $data
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET THEME
    public function get_theme($slug)
    {
        try {
            // Cari profil berdasarkan slug
            $profil = Profil::where('slug', $slug)->firstOrFail();

            // Check user
            $user = User::find($profil->user_id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan pada masjid ini !'], 404);
            }

            // Check theme
            $theme = Theme::find($user->theme_id);
            if (!$theme) {
                return response()->json(['success' => false, 'message' => 'Tema tidak ditemukan pada user ini !'], 404);
            }

            // Pastikan updated_at ada, gunakan timestamp default jika null
            $updatedAt = $theme->updated_at ? $theme->updated_at->timestamp : now()->timestamp;

            $data = [
                'theme_id' => $theme->id,
                'updated_at' => $updatedAt,
                'css_file' => $theme->css_file ? asset($theme->css_file) : asset('css/style.css') // Tambahkan css_file
            ];
            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data theme masjid !',
                'data' => $data
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET MARQUEE (API LAMA)
    public function get_marquee1($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();
            $marquee = Marquee::where('user_id', $profil->user_id)->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => [
                    'marquee1' => $marquee->marquee1,
                    'marquee2' => $marquee->marquee2,
                    'marquee3' => $marquee->marquee3,
                    'marquee4' => $marquee->marquee4,
                    'marquee5' => $marquee->marquee5,
                    'marquee6' => $marquee->marquee6
                ]
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil / Marquee tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET MARQUEE
    public function get_marquee($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();
            $marquee = Marquee::where('user_id', $profil->user_id)->firstOrFail();
            $data = [];
            if ($marquee->marquee1) $data[] = $marquee->marquee1;
            if ($marquee->marquee2) $data[] = $marquee->marquee2;
            if ($marquee->marquee3) $data[] = $marquee->marquee3;
            if ($marquee->marquee4) $data[] = $marquee->marquee4;
            if ($marquee->marquee5) $data[] = $marquee->marquee5;
            if ($marquee->marquee6) $data[] = $marquee->marquee6;

            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data marquee masjid !',
                'data' => $data
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil / Marquee tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET REKAPAN total keseluruhan kategori per profile (id_masjid),
    public function get_balance_summary(Request $request, $slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();

            // Query params: details (none|summary|full), page, per_page
            $details = strtolower($request->query('details', 'summary'));
            if (!in_array($details, ['none', 'summary', 'full'])) {
                $details = 'summary';
            }
            $page = max(1, (int) $request->query('page', 1));
            $perPage = (int) $request->query('per_page', 50);
            if ($perPage < 1) {
                $perPage = 50;
            }
            if ($perPage > 500) {
                $perPage = 500;
            } // batas atas untuk mencegah respons terlalu besar
            // Limit item per kategori pada mode full_month hanya jika recent_limit diberikan (clamp 1..50; default tidak ada limit)
            $hasRecentLimit = $request->has('recent_limit');
            $recentLimit = null;
            if ($hasRecentLimit) {
                $recentLimit = (int) $request->query('recent_limit');
                if ($recentLimit < 1) {
                    $recentLimit = 3;
                }
                if ($recentLimit > 50) {
                    $recentLimit = 50;
                }
            }
            // parameter tambahan: full_month => bila true, detail full dibatasi bulan berjalan tanpa paginasi
            $fullMonth = filter_var($request->query('full_month', 'false'), FILTER_VALIDATE_BOOLEAN);
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();
            $fullMonth = filter_var($request->query('full_month', 'false'), FILTER_VALIDATE_BOOLEAN);
            // keep ISO format for DB query, but expose dd-mm-yyyy in response
            $startOfMonthIso = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonthIso = Carbon::now()->endOfMonth()->toDateString();
            $startOfMonthDisplay = Carbon::parse($startOfMonthIso)->format('d-m-Y');
            $endOfMonthDisplay = Carbon::parse($endOfMonthIso)->format('d-m-Y');
            // Tambahan: nama bulan (Indonesia) dan tahun untuk period saat full_month=true
            $monthNameId = Carbon::now()->locale('id')->translatedFormat('F');
            $yearNumber = Carbon::now()->year;
            // Pada mode full_month, jangan sertakan running_balance untuk mengurangi payload
            $skipRunningBalance = $fullMonth;
            // Tambahan: dukung paginasi saat full_month=true bila page/per_page diberikan dan tanpa recent_limit
            $monthPaginationRequested = $fullMonth && !$hasRecentLimit && ($request->has('page') || $request->has('per_page'));

            $rows = DB::table('tb_balance')
                ->leftJoin('group_categories', 'group_categories.id', '=', 'tb_balance.id_group_category')
                ->where('tb_balance.id_masjid', $profil->id)
                ->selectRaw('tb_balance.id_group_category as category_id, COALESCE(group_categories.name, "-") as category_name, tb_balance.total_masuk as sumMasuk, tb_balance.total_keluar as sumKeluar, tb_balance.ending_balance as ending')
                ->orderBy('group_categories.name')
                ->get();

            $categories = [];
            $categoriesWithItems = [];
            $grandTotals = ['sumMasuk' => 0, 'sumKeluar' => 0, 'ending' => 0];

            foreach ($rows as $row) {
                $sumMasuk = (int) ($row->sumMasuk ?? 0);
                $sumKeluar = (int) ($row->sumKeluar ?? 0);
                $ending = (int) ($row->ending ?? 0);
                $categories[] = [
                    'categoryId' => (int) $row->category_id,
                    'categoryName' => $row->category_name,
                    'sumMasuk' => $sumMasuk,
                    'sumKeluar' => $sumKeluar,
                    'ending' => $ending,
                    // tambahan: tampilan terformat ribuan
                    'sumMasukDisplay' => 'Rp ' . number_format($sumMasuk, 0, ',', '.'),
                    'sumKeluarDisplay' => 'Rp ' . number_format($sumKeluar, 0, ',', '.'),
                    'endingDisplay' => 'Rp ' . number_format($ending, 0, ',', '.'),
                ];

                if ($details === 'full') {
                    // Pagination per kategori
                    if ($fullMonth) {
                        $totalItems = DB::table('laporans')
                            ->where('laporans.id_masjid', $profil->id)
                            ->where('laporans.id_group_category', $row->category_id)
                            ->whereBetween('laporans.tanggal', [$startOfMonth, $endOfMonth])
                            ->count();

                        // Hanya kirim N item terbaru (urut terbaru) untuk mengurangi payload
                        $selectCols = 'laporans.id, laporans.id_masjid, laporans.id_group_category, laporans.tanggal, laporans.uraian, laporans.jenis, laporans.saldo, laporans.is_opening' . ($skipRunningBalance ? '' : ', laporans.running_balance');
                        $queryItems = DB::table('laporans')
                            ->where('laporans.id_masjid', $profil->id)
                            ->where('laporans.id_group_category', $row->category_id)
                            ->whereBetween('laporans.tanggal', [$startOfMonth, $endOfMonth])
                            ->selectRaw($selectCols)
                            ->orderBy('tanggal', 'desc')
                            ->orderBy('id', 'desc');

                        if ($hasRecentLimit && $recentLimit !== null) {
                            $queryItems->limit($recentLimit);
                        }
                        if ($monthPaginationRequested && !$hasRecentLimit) {
                            $queryItems->offset(($page - 1) * $perPage)->limit($perPage);
                        }

                        $itemRows = $queryItems->get();

                        $items = [];
                        $startNo = ($monthPaginationRequested && !$hasRecentLimit) ? (($page - 1) * $perPage) : 0; // nomor awal saat paginasi
                    } else {
                        $totalItems = DB::table('laporans')
                            ->where('laporans.id_masjid', $profil->id)
                            ->where('laporans.id_group_category', $row->category_id)
                            ->count();

                        $itemRows = DB::table('laporans')
                            ->where('laporans.id_masjid', $profil->id)
                            ->where('laporans.id_group_category', $row->category_id)
                            ->selectRaw('laporans.id, laporans.id_masjid, laporans.id_group_category, laporans.tanggal, laporans.uraian, laporans.jenis, laporans.saldo, laporans.is_opening, laporans.running_balance')
                            ->orderBy('tanggal', 'asc')
                            ->orderBy('id', 'asc')
                            ->offset(($page - 1) * $perPage)
                            ->limit($perPage)
                            ->get();

                        $items = [];
                        $startNo = ($page - 1) * $perPage; // dengan paginasi
                    }
                    foreach ($itemRows as $i => $lap) {
                        $masuk = 0;
                        $keluar = 0;
                        if ($lap->is_opening) {
                            $masuk = (int) $lap->saldo;
                        } else {
                            if ($lap->jenis === 'masuk') {
                                $masuk = (int) $lap->saldo;
                            } elseif ($lap->jenis === 'keluar') {
                                $keluar = (int) $lap->saldo;
                            }
                        }
                        $item = [
                            'id' => (int) $lap->id,
                            'no' => (int) $startNo + (int) $i + 1,
                            'tanggal' => Carbon::parse($lap->tanggal)->format('d/m/Y'),
                            'uraian' => $lap->is_opening ? ($lap->uraian ?: 'Sisa bulan yang lalu') : $lap->uraian,
                            'is_opening' => (bool) $lap->is_opening,
                            'groupCategoryName' => $row->category_name,
                            'masukDisplay' => $masuk > 0 ? ('Rp ' . number_format($masuk, 0, ',', '.')) : '-',
                            'keluarDisplay' => $keluar > 0 ? ('Rp ' . number_format($keluar, 0, ',', '.')) : '-',
                        ];
                        if (!$skipRunningBalance) {
                            $item['runningBalanceDisplay'] = 'Rp ' . number_format((int) ($lap->running_balance ?? 0), 0, ',', '.');
                        }
                        $items[] = $item;
                    }

                    $categoryBlock = [
                        'categoryId' => (int) $row->category_id,
                        'categoryName' => $row->category_name,
                        'items' => $items,
                        'totals' => [
                            'totalMasukDisplay' => 'Rp ' . number_format($sumMasuk, 0, ',', '.'),
                            'totalKeluarDisplay' => 'Rp ' . number_format($sumKeluar, 0, ',', '.'),
                            'endingBalanceDisplay' => 'Rp ' . number_format($ending, 0, ',', '.'),
                        ],
                    ];

                    if (!$fullMonth) {
                        $categoryBlock['pagination'] = [
                            'page' => $page,
                            'perPage' => $perPage,
                            'total' => (int) $totalItems,
                            'lastPage' => (int) ceil($totalItems / $perPage),
                        ];
                    } elseif ($monthPaginationRequested) {
                        $categoryBlock['pagination'] = [
                            'page' => $page,
                            'perPage' => $perPage,
                            'total' => (int) $totalItems,
                            'lastPage' => (int) ceil($totalItems / $perPage),
                        ];
                    }

                    $categoriesWithItems[] = $categoryBlock;
                }

                $grandTotals['sumMasuk'] += $sumMasuk;
                $grandTotals['sumKeluar'] += $sumKeluar;
                $grandTotals['ending'] += $ending;
            }

            // tambahan: grandTotals tampilan terformat ribuan
            $grandTotals['sumMasukDisplay'] = 'Rp ' . number_format($grandTotals['sumMasuk'], 0, ',', '.');
            $grandTotals['sumKeluarDisplay'] = 'Rp ' . number_format($grandTotals['sumKeluar'], 0, ',', '.');
            $grandTotals['endingDisplay'] = 'Rp ' . number_format($grandTotals['ending'], 0, ',', '.');

            $data = [
                'profil' => [
                    'id' => $profil->id,
                    'name' => $profil->name,
                ],
                'categories' => $categories,
                // hanya sertakan detail ketika diminta
                // 'categoriesWithItems' akan ditambahkan di bawah bila $details === 'full'
                'grandTotals' => $grandTotals,
            ];

            if ($details === 'full') {
                $data['categoriesWithItems'] = $categoriesWithItems;
                $data['details'] = 'full';
                $data['full_month'] = $fullMonth;
                if (!$fullMonth) {
                    $data['page'] = $page;
                    $data['per_page'] = $perPage;
                } else {
                    $data['period'] = [
                        'type' => 'current_month',
                        'month' => $monthNameId,
                        'year' => (int) $yearNumber,
                        'start' => $startOfMonthDisplay,
                        'end' => $endOfMonthDisplay,
                    ];
                    if ($monthPaginationRequested) {
                        $data['page'] = $page;
                        $data['per_page'] = $perPage;
                    }
                }
            } else {
                $data['details'] = $details; // none atau summary
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data rekap balance masjid !',
                'data' => $data
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // NEW SLIDER by Mazlan
    public function get_new_slider($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();
            $newSlider = NewSlider::where('uploaded_by', $profil->user_id)->get();
            $sliderPaths = $newSlider->isNotEmpty()
                ? $newSlider->map(fn($item) => asset($item->path))
                : collect([asset('images/other/slide-jws-default.jpg')]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data slide masjid !',
                'data' => $sliderPaths
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Slider / New Slider tidak ditemukan !'
            ], 404);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => addslashes($ex->getMessage())
            ], 500);
        }
    }

    // GET SLIDES (API LAMA)
    public function get_slides1($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();
            $slides = Slides::where('user_id', $profil->user_id)->firstOrFail();
            return response()->json([
                'success' => true,
                'data' => [
                    'slide1' => $slides->slide1,
                    'slide2' => $slides->slide2,
                    'slide3' => $slides->slide3,
                    'slide4' => $slides->slide4,
                    'slide5' => $slides->slide5,
                    'slide6' => $slides->slide6
                ]
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil / Slides tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET SLIDES
    public function get_slides($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();
            $slides = Slides::where('user_id', $profil->user_id)->first();
            $data = [];
            if ($slides) {
                if ($slides->slide1) $data[] = asset($slides->slide1);
                if ($slides->slide2) $data[] = asset($slides->slide2);
                if ($slides->slide3) $data[] = asset($slides->slide3);
                if ($slides->slide4) $data[] = asset($slides->slide4);
                if ($slides->slide5) $data[] = asset($slides->slide5);
                if ($slides->slide6) $data[] = asset($slides->slide6);
            }
            // Default
            else {
                $data[] = asset('images/other/slide-jws-default.jpg');
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data slide masjid !',
                'data' => $data
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET PETUGAS
    public function get_petugas($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();
            // $petugas = Petugas::where('user_id', $profil->user_id)->firstOrFail();
            // $data = [
            //     'hari' => $petugas->hari,
            //     'khatib' => $petugas->khatib,
            //     'imam' => $petugas->imam,
            //     'muadzin' => $petugas->muadzin
            // ];
            $data = Petugas::where('user_id', $profil->user_id)->orderBy('hari', 'DESC')->get(['hari', 'khatib', 'imam', 'muadzin']);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data petugas jumat masjid !',
                'data' => $data
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil / Petugas tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET ADZAN (API LAMA)
    public function get_adzan1($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();
            $adzan = Adzan::where('user_id', $profil->user_id)->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'adzan1' => $adzan->adzan1,
                    'adzan2' => $adzan->adzan2,
                    'adzan3' => $adzan->adzan3,
                    'adzan4' => $adzan->adzan4,
                    'adzan5' => $adzan->adzan5,
                    'adzan6' => $adzan->adzan6,
                    'adzan15' => $adzan->adzan15,
                    'adzan7' => $adzan->adzan7,
                    'adzan8' => $adzan->adzan8,
                    'adzan9' => $adzan->adzan9,
                    'adzan10' => $adzan->adzan10,
                    'adzan11' => $adzan->adzan11,
                    'adzan12' => $adzan->adzan12,
                ]
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil / Adzan tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET ADZAN
    public function get_adzan($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();
            $adzan = Adzan::where('user_id', $profil->user_id)->first();
            $durasi = Durasi::where('user_id', $profil->user_id)->first();

            $defaultDurasi = [
                'syuruq' => 2 * 60,
                'shubuh' => [
                    'adzan' => 1 * 60,
                    'iqomah' => 1 * 60,
                    'final' => 1 * 30,
                ],
                'dzuhur' => [
                    'adzan' => 1 * 60,
                    'iqomah' => 1 * 60,
                    'final' => 1 * 30,
                ],
                'jumat' => 1 * 60,
                'ashar' => [
                    'adzan' => 1 * 60,
                    'iqomah' => 1 * 60,
                    'final' => 1 * 30,
                ],
                'maghrib' => [
                    'adzan' => 1 * 60,
                    'iqomah' => 1 * 60,
                    'final' => 1 * 30,
                ],
                'isya' => [
                    'adzan' => 1 * 60,
                    'iqomah' => 1 * 60,
                    'final' => 1 * 30,
                ],
            ];

            // Default arrays
            $default['5waktu'] = [
                asset('/images/other/doa-setelah-adzan-default.webp'),
                asset('/images/other/doa-masuk-masjid-default.webp'),
                asset('/images/other/non-silent-hp-default.webp'),
            ];
            $default['jumat'] = [
                asset('/images/other/doa-setelah-adzan-default.webp'),
                asset('/images/other/doa-masuk-masjid-default.webp'),
                asset('/images/other/dilarang-bicara-saat-sholat-jumat-default.webp'),
                asset('/images/other/non-silent-hp-default.webp'),
            ];
            $default['final'] = asset('images/other/lurus-rapat-shaf-default.webp');

            if ($adzan) {
                // 5 Waktu
                $data['5waktu'] = array_filter([
                    $adzan->adzan1 ? asset($adzan->adzan1) : null,
                    $adzan->adzan2 ? asset($adzan->adzan2) : null,
                    $adzan->adzan3 ? asset($adzan->adzan3) : null,
                    $adzan->adzan4 ? asset($adzan->adzan4) : null,
                    $adzan->adzan5 ? asset($adzan->adzan5) : null,
                    $adzan->adzan6 ? asset($adzan->adzan6) : null,
                ]);
                if (empty($data['5waktu']))
                    $data['5waktu'] = $default['5waktu'];

                // Jumat
                $data['jumat'] = array_filter([
                    $adzan->adzan7 ? asset($adzan->adzan7) : null,
                    $adzan->adzan8 ? asset($adzan->adzan8) : null,
                    $adzan->adzan9 ? asset($adzan->adzan9) : null,
                    $adzan->adzan10 ? asset($adzan->adzan10) : null,
                    $adzan->adzan11 ? asset($adzan->adzan11) : null,
                    $adzan->adzan12 ? asset($adzan->adzan12) : null,
                ]);
                if (empty($data['jumat']))
                    $data['jumat'] = $default['jumat'];

                // Final
                $data['final'] = $adzan->adzan15 ? asset($adzan->adzan15) : $default['final'];
            } else {
                $data['5waktu'] = $default['5waktu'];
                $data['jumat'] = $default['jumat'];
                $data['final'] = $default['final'];
            }

            if ($durasi) {
                $dataDurasi = [
                    'syuruq' => $durasi->adzan_shuruq ? $durasi->adzan_shuruq * 60 : $defaultDurasi['syuruq'],
                    'shubuh' => [
                        'adzan' => $durasi->adzan_shubuh ? $durasi->adzan_shubuh * 60 : $defaultDurasi['shubuh']['adzan'],
                        'iqomah' => $durasi->iqomah_shubuh ? $durasi->iqomah_shubuh * 60 : $defaultDurasi['shubuh']['iqomah'],
                        'final' => $durasi->final_shubuh ? $durasi->final_shubuh * 60 : $defaultDurasi['shubuh']['final'],
                    ],
                    'dzuhur' => [
                        'adzan' => $durasi->adzan_dzuhur ? $durasi->adzan_dzuhur * 60 : $defaultDurasi['dzuhur']['adzan'],
                        'iqomah' => $durasi->iqomah_dzuhur ? $durasi->iqomah_dzuhur * 60 : $defaultDurasi['dzuhur']['iqomah'],
                        'final' => $durasi->final_dzuhur ? $durasi->final_dzuhur * 60 : $defaultDurasi['dzuhur']['final'],
                    ],
                    'jumat' => $durasi->jumat_slide ? $durasi->jumat_slide * 60 : $defaultDurasi['jumat'],
                    'ashar' => [
                        'adzan' => $durasi->adzan_ashar ? $durasi->adzan_ashar * 60 : $defaultDurasi['ashar']['adzan'],
                        'iqomah' => $durasi->iqomah_ashar ? $durasi->iqomah_ashar * 60 : $defaultDurasi['ashar']['iqomah'],
                        'final' => $durasi->final_ashar ? $durasi->final_ashar * 60 : $defaultDurasi['ashar']['final'],
                    ],
                    'maghrib' => [
                        'adzan' => $durasi->adzan_maghrib ? $durasi->adzan_maghrib * 60 : $defaultDurasi['maghrib']['adzan'],
                        'iqomah' => $durasi->iqomah_maghrib ? $durasi->iqomah_maghrib * 60 : $defaultDurasi['maghrib']['iqomah'],
                        'final' => $durasi->final_maghrib ? $durasi->final_maghrib * 60 : $defaultDurasi['maghrib']['final'],
                    ],
                    'isya' => [
                        'adzan' => $durasi->adzan_isya ? $durasi->adzan_isya * 60 : $defaultDurasi['isya']['adzan'],
                        'iqomah' => $durasi->iqomah_isya ? $durasi->iqomah_isya * 60 : $defaultDurasi['isya']['iqomah'],
                        'final' => $durasi->final_isya ? $durasi->final_isya * 60 : $defaultDurasi['isya']['final'],
                    ],
                ];
                $data['durasi'] = $dataDurasi;
            } else {
                $data['durasi'] = $defaultDurasi;
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data slide adzan, slide iqomah dan durasi masjid !',
                'data' => $data
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil / Adzan tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET AUDIO BACKGROUND (API LAMA)
    public function get_audio1($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();
            $audio = Audios::where('user_id', $profil->user_id)->firstOrFail();
            if (!$audio->status) {
                return response()->json(['success' => false, 'message' => 'Audio background tidak diaktifkan pada masjid ini !'], 404);
            }

            // Buat instance komponen Audio untuk menggunakan generateLocalUrl
            $audioComponent = new \App\Livewire\Audios\Audio();
            return response()->json([
                'success' => true,
                'data' => [
                    'audio1' => $audio->audio1 ? $audioComponent->generateLocalUrl($audio->audio1) : null,
                    'audio2' => $audio->audio2 ? $audioComponent->generateLocalUrl($audio->audio2) : null,
                    'audio3' => $audio->audio3 ? $audioComponent->generateLocalUrl($audio->audio3) : null,
                    'status' => $audio->status
                ]
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil / Audio Background tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET AUDIO BACKGROUND
    public function get_audio($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();
            $audio = Audios::where('user_id', $profil->user_id)->firstOrFail();
            if (!$audio->status) {
                return response()->json(['success' => false, 'message' => 'Audio background tidak diaktifkan pada masjid ini !'], 404);
            }

            // Buat instance komponen Audio untuk menggunakan generateLocalUrl
            $audioComponent = new \App\Livewire\Audios\Audio();
            $data = [];
            if ($audio->audio1) $data[] = $audioComponent->generateLocalUrl($audio->audio1);
            if ($audio->audio2) $data[] = $audioComponent->generateLocalUrl($audio->audio2);
            if ($audio->audio3) $data[] = $audioComponent->generateLocalUrl($audio->audio3);
            // $data = [
            //     'audio1' => $audio->audio1 ? $audioComponent->generateLocalUrl($audio->audio1) : null,
            //     'audio2' => $audio->audio2 ? $audioComponent->generateLocalUrl($audio->audio2) : null,
            //     'audio3' => $audio->audio3 ? $audioComponent->generateLocalUrl($audio->audio3) : null,
            //     'status' => $audio->status
            // ];

            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data audio background masjid !',
                'data' => $data
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil / Audio Background tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }

    // GET AUDIO ADZAN
    public function get_adzan_audio($slug)
    {
        try {
            $profil = Profil::where('slug', $slug)->firstOrFail();

            $adzanaudio = AdzanAudio::where('user_id', $profil->user_id)->firstOrFail();
            if (!$adzanaudio->status) {
                return response()->json(['success' => false, 'message' => 'Audio adzan tidak diaktifkan pada masjid ini !'], 404);
            }

            // Buat instance komponen Audio untuk menggunakan generateLocalUrl
            $audioComponent = new \App\Livewire\AdzanAudio\AdzanAudio();
            $data = [
                'adzan_audio' => $adzanaudio->audioadzan ? $audioComponent->generateLocalUrl($adzanaudio->audioadzan) : null,
                'adzan_shubuh' => $adzanaudio->adzanshubuh ? $audioComponent->generateLocalUrl($adzanaudio->adzanshubuh) : null,
                // 'status' => $adzanaudio->status
            ];

            return response()->json([
                'success' => true,
                'message' => 'Berhasil get data audio adzan masjid !',
                'data' => $data
            ]);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['success' => false, 'message' => 'Profil / Audio Adzan tidak ditemukan !'], 404);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'message' => addslashes($ex->getMessage())], 500);
        }
    }




    // GET PRAYER STATUS
    public function get_prayer_status($slug)
    {
        try {
            // Get the Firdaus component instance
            $firdaus = new \App\Livewire\Firdaus\Firdaus();
            $firdaus->mount($slug);

            try {
                // Gunakan waktu server langsung dengan Carbon sebagai sumber utama
                $jakartaDateTime = Carbon::now('Asia/Jakarta');
                $currentTime = $jakartaDateTime->format('H:i');
            } catch (\Exception $e) {
                // Jika gagal menggunakan Carbon, coba API eksternal sebagai fallback
                try {
                    // Get current server time from external API as fallback
                    $response = Http::get('https://superapp.pekanbaru.go.id/api/server-time');
                    if (!$response->successful()) {
                        return response()->json(['success' => false, 'message' => 'Server time unavailable']);
                    }

                    $serverTime = $response['serverTime'];

                    // Convert UTC time to Asia/Jakarta timezone
                    $utcDateTime = new \DateTime($serverTime, new \DateTimeZone('UTC'));
                    $jakartaDateTime = $utcDateTime->setTimezone(new \DateTimeZone('Asia/Jakarta'));
                    $currentTime = $jakartaDateTime->format('H:i');
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => 'Failed to get server time: ' . $e->getMessage()]);
                }
            }

            // Get prayer status using reflection to call private method
            $reflection = new \ReflectionClass($firdaus);
            $method = $reflection->getMethod('calculateActivePrayerTimeStatus');
            $method->setAccessible(true);
            $status = $method->invoke($firdaus, $currentTime);

            return response()->json([
                'success' => true,
                'data' => $status,
                'current_time_jakarta' => $jakartaDateTime->format('Y-m-d H:i:s'), // Optional: untuk debugging
                'timezone' => 'Asia/Jakarta'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating prayer status: ' . $e->getMessage()
            ]);
        }
    }
}
