<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TienIch;

class TienIchController extends Controller
{
    public function index()
    {
        $danhSach = TienIch::orderBy('loai', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $danhSach
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ten_tien_ich' => 'required|string|max:100|unique:tien_ich,ten_tien_ich',
            'loai' => 'required|in:co_ban,noi_that,an_ninh'
        ]);

        $tienIch = TienIch::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Đã thêm tiện ích mới!',
            'data' => $tienIch
        ]);
    }

    public function destroy($id)
    {
        $tienIch = TienIch::find($id);
        if ($tienIch) {
            $tienIch->delete();
            return response()->json(['status' => 'success', 'message' => 'Đã xóa tiện ích!']);
        }
        return response()->json(['status' => 'error', 'message' => 'Không tìm thấy tiện ích!'], 404);
    }
}