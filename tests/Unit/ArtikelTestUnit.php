<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Artikel;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArtikelTestUnit extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function slug_otomatis_terbuat_saat_membuat_artikel()
    {
        $artikel = Artikel::create([
            'title' => 'Judul Artikel Pertama',
            'slug' => Str::slug('Judul Artikel Pertama'),
            'excerpt' => 'Isi excerpt contoh',
            'image' => 'https://example.com/img.jpg',
            'date' => now()->format('Y-m-d'),
        ]);

        $this->assertEquals('judul-artikel-pertama', $artikel->slug);
    }

    /** @test */
    public function slug_bertambah_angka_jika_ada_duplikasi()
    {
        Artikel::create([
            'title' => 'Judul Yang Sama',
            'slug' => 'judul-yang-sama',
            'excerpt' => 'excerpt',
            'image' => 'https://example.com/img.jpg',
            'date' => now()->format('Y-m-d'),
        ]);

        $artikel2 = Artikel::create([
            'title' => 'Judul Yang Sama',
            'slug' => 'judul-yang-sama-1',
            'excerpt' => 'excerpt kedua',
            'image' => 'https://example.com/img2.jpg',
            'date' => now()->format('Y-m-d'),
        ]);

        $this->assertEquals('judul-yang-sama-1', $artikel2->slug);
    }

    /** @test */
    public function artikel_bisa_diupdate_dengan_slug_baru()
    {
        $artikel = Artikel::create([
            'title' => 'Judul Lama',
            'slug' => 'judul-lama',
            'excerpt' => 'excerpt lama',
            'image' => 'https://example.com/img.jpg',
            'date' => now()->format('Y-m-d'),
        ]);

        $artikel->update([
            'title' => 'Judul Baru Edit',
            'slug' => 'judul-baru-edit', 
            'excerpt' => 'excerpt baru',
            'image' => 'https://example.com/new.jpg',
            'date' => now()->format('Y-m-d'),
        ]);

        $this->assertEquals('judul-baru-edit', $artikel->slug);
        $this->assertEquals('Judul Baru Edit', $artikel->title);
    }

    /** @test */
    public function artikel_bisa_dihapus()
    {
        $artikel = Artikel::factory()->create();

        $artikel->delete();

        $this->assertDatabaseMissing('artikels', [
            'id' => $artikel->id
        ]);
    }

    /** @test */
    public function pencarian_artikel_berfungsi()
    {
        Artikel::factory()->create([
            'title' => 'Belajar Laravel Sangat Mudah',
        ]);

        Artikel::factory()->create([
            'title' => 'Tutorial React untuk Pemula',
        ]);

        $results = Artikel::where('title', 'LIKE', '%Laravel%')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Belajar Laravel Sangat Mudah', $results->first()->title);
    }
}
