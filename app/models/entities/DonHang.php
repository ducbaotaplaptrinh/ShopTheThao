<?php

namespace app\models\entities;

use DateTime;

class DonHang
{
    private ?int $id = null;
    private int $ma_nguoi_dung = 0;
    private string $ma_don_hang = "";
    private string $ho_ten_nguoi_nhan = "";
    private string $so_dien_thoai = "";
    private ?string $email = null;
    private string $dia_chi_giao_hang = "";
    private ?string $ghi_chu = null;
    private float $tong_tien_hang = 0.0;
    private float $phi_van_chuyen = 0.0;
    private float $tien_giam_gia = 0.0;
    private float $tong_thanh_toan = 0.0;
    private string $phuong_thuc_thanh_toan = "cod";
    private string $trang_thai_thanh_toan = "chua_thanh_toan";
    private string $trang_thai_don_hang = "cho_xac_nhan";
    private ?DateTime $ngay_tao = null;
    private ?DateTime $ngay_cap_nhat = null;
    private ?string $ma_giam_gia = null;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->ma_nguoi_dung = (int)($data['ma_nguoi_dung'] ?? 0);
            $this->ma_don_hang = $data['ma_don_hang'] ?? "";
            $this->ho_ten_nguoi_nhan = $data['ho_ten_nguoi_nhan'] ?? "";
            $this->so_dien_thoai = $data['so_dien_thoai'] ?? "";
            $this->email = $data['email'] ?? null;
            $this->dia_chi_giao_hang = $data['dia_chi_giao_hang'] ?? "";
            $this->ghi_chu = $data['ghi_chu'] ?? null;
            $this->tong_tien_hang = (float)($data['tong_tien_hang'] ?? 0.0);
            $this->phi_van_chuyen = (float)($data['phi_van_chuyen'] ?? 0.0);
            $this->tien_giam_gia = (float)($data['tien_giam_gia'] ?? 0.0);
            $this->tong_thanh_toan = (float)($data['tong_thanh_toan'] ?? 0.0);
            $this->phuong_thuc_thanh_toan = $data['phuong_thuc_thanh_toan'] ?? "cod";
            $this->trang_thai_thanh_toan = $data['trang_thai_thanh_toan'] ?? "chua_thanh_toan";
            $this->trang_thai_don_hang = $data['trang_thai_don_hang'] ?? "cho_xac_nhan";
            $this->ma_giam_gia = $data['ma_giam_gia'] ?? null;
            
            $this->ngay_tao = isset($data['ngay_tao']) && !empty($data['ngay_tao'])
                ? new DateTime($data['ngay_tao']) : null;
            $this->ngay_cap_nhat = isset($data['ngay_cap_nhat']) && !empty($data['ngay_cap_nhat'])
                ? new DateTime($data['ngay_cap_nhat']) : null;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getMa_nguoi_dung(): int
    {
        return $this->ma_nguoi_dung;
    }

    public function setMa_nguoi_dung(int $ma_nguoi_dung): self
    {
        $this->ma_nguoi_dung = $ma_nguoi_dung;
        return $this;
    }

    public function getMa_don_hang(): string
    {
        return $this->ma_don_hang;
    }

    public function setMa_don_hang(string $ma_don_hang): self
    {
        $this->ma_don_hang = $ma_don_hang;
        return $this;
    }

    public function getHo_ten_nguoi_nhan(): string
    {
        return $this->ho_ten_nguoi_nhan;
    }

    public function setHo_ten_nguoi_nhan(string $ho_ten_nguoi_nhan): self
    {
        $this->ho_ten_nguoi_nhan = $ho_ten_nguoi_nhan;
        return $this;
    }

    public function getSo_dien_thoai(): string
    {
        return $this->so_dien_thoai;
    }

    public function setSo_dien_thoai(string $so_dien_thoai): self
    {
        $this->so_dien_thoai = $so_dien_thoai;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getDia_chi_giao_hang(): string
    {
        return $this->dia_chi_giao_hang;
    }

    public function setDia_chi_giao_hang(string $dia_chi_giao_hang): self
    {
        $this->dia_chi_giao_hang = $dia_chi_giao_hang;
        return $this;
    }

    public function getGhi_chu(): ?string
    {
        return $this->ghi_chu;
    }

    public function setGhi_chu(?string $ghi_chu): self
    {
        $this->ghi_chu = $ghi_chu;
        return $this;
    }

    public function getTong_tien_hang(): float
    {
        return $this->tong_tien_hang;
    }

    public function setTong_tien_hang(float $tong_tien_hang): self
    {
        $this->tong_tien_hang = $tong_tien_hang;
        return $this;
    }

    public function getPhi_van_chuyen(): float
    {
        return $this->phi_van_chuyen;
    }

    public function setPhi_van_chuyen(float $phi_van_chuyen): self
    {
        $this->phi_van_chuyen = $phi_van_chuyen;
        return $this;
    }

    public function getTien_giam_gia(): float
    {
        return $this->tien_giam_gia;
    }

    public function setTien_giam_gia(float $tien_giam_gia): self
    {
        $this->tien_giam_gia = $tien_giam_gia;
        return $this;
    }

    public function getTong_thanh_toan(): float
    {
        return $this->tong_thanh_toan;
    }

    public function setTong_thanh_toan(float $tong_thanh_toan): self
    {
        $this->tong_thanh_toan = $tong_thanh_toan;
        return $this;
    }

    public function getPhuong_thuc_thanh_toan(): string
    {
        return $this->phuong_thuc_thanh_toan;
    }

    public function setPhuong_thuc_thanh_toan(string $phuong_thuc_thanh_toan): self
    {
        $this->phuong_thuc_thanh_toan = $phuong_thuc_thanh_toan;
        return $this;
    }

    public function getTrang_thai_thanh_toan(): string
    {
        return $this->trang_thai_thanh_toan;
    }

    public function setTrang_thai_thanh_toan(string $trang_thai_thanh_toan): self
    {
        $this->trang_thai_thanh_toan = $trang_thai_thanh_toan;
        return $this;
    }

    public function getTrang_thai_don_hang(): string
    {
        return $this->trang_thai_don_hang;
    }

    public function setTrang_thai_don_hang(string $trang_thai_don_hang): self
    {
        $this->trang_thai_don_hang = $trang_thai_don_hang;
        return $this;
    }

    public function getNgay_tao(): ?DateTime
    {
        return $this->ngay_tao;
    }

    public function setNgay_tao(?DateTime $ngay_tao): self
    {
        $this->ngay_tao = $ngay_tao;
        return $this;
    }

    public function getNgay_cap_nhat(): ?DateTime
    {
        return $this->ngay_cap_nhat;
    }

    public function setNgay_cap_nhat(?DateTime $ngay_cap_nhat): self
    {
        $this->ngay_cap_nhat = $ngay_cap_nhat;
        return $this;
    }

    public function getMa_giam_gia(): ?string
    {
        return $this->ma_giam_gia;
    }

    public function setMa_giam_gia(?string $ma_giam_gia): self
    {
        $this->ma_giam_gia = $ma_giam_gia;
        return $this;
    }
}
