<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Artikel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArtikelControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_create_artikel_with_valid_data()
    {
        $data = [
            'title' => 'Ini Adalah Judul Artikel Yang Valid',
            'excerpt' => 'Ini adalah excerpt artikel',
            'image' => 'https://example.com/image.jpg',
            'date' => now()->subDays(2)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user)
                         ->post(route('dashboard.artikel.store'), $data);

        $response->assertRedirect(route('dashboard.artikel.index'));
        
        $this->assertDatabaseHas('artikels', [
            'title' => 'Ini Adalah Judul Artikel Yang Valid',
            'slug' => 'ini-adalah-judul-artikel-yang-valid',
        ]);
    }

    /** @test */
    public function it_validates_title_minimum_10_characters()
    {
        $response = $this->actingAs($this->user)
                         ->post(route('dashboard.artikel.store'), [
                             'title' => 'Pendek',
                             'excerpt' => 'Excerpt test',
                             'image' => 'https://example.com/image.jpg',
                             'date' => now()->format('Y-m-d'),
                         ]);

        $response->assertSessionHasErrors(['title']);
    }

    /** @test */
    public function it_validates_title_maximum_60_characters()
    {
        $response = $this->actingAs($this->user)
                         ->post(route('dashboard.artikel.store'), [
                             'title' => str_repeat('a', 61),
                             'excerpt' => 'Excerpt test',
                             'image' => 'https://example.com/image.jpg',
                             'date' => now()->format('Y-m-d'),
                         ]);

        $response->assertSessionHasErrors(['title']);
    }

    /** @test */
    public function it_validates_image_must_be_url()
    {
        $response = $this->actingAs($this->user)
                         ->post(route('dashboard.artikel.store'), [
                             'title' => 'Judul Valid Sepuluh Karakter',
                             'excerpt' => 'Excerpt test',
                             'image' => 'bukan-url',
                             'date' => now()->format('Y-m-d'),
                         ]);

        $response->assertSessionHasErrors(['image']);
    }

    /** @test */
    public function it_validates_date_minimum_5_days_ago()
    {
        $response = $this->actingAs($this->user)
                         ->post(route('dashboard.artikel.store'), [
                             'title' => 'Judul Valid Sepuluh Karakter',
                             'excerpt' => 'Excerpt test',
                             'image' => 'https://example.com/image.jpg',
                             'date' => now()->subDays(6)->format('Y-m-d'),
                         ]);

        $response->assertSessionHasErrors(['date']);
    }

    /** @test */
    public function it_validates_date_maximum_today()
    {
        $response = $this->actingAs($this->user)
                         ->post(route('dashboard.artikel.store'), [
                             'title' => 'Judul Valid Sepuluh Karakter',
                             'excerpt' => 'Excerpt test',
                             'image' => 'https://example.com/image.jpg',
                             'date' => now()->addDay()->format('Y-m-d'),
                         ]);

        $response->assertSessionHasErrors(['date']);
    }

    /** @test */
    public function it_can_update_artikel()
    {
        $artikel = Artikel::factory()->create();

        $data = [
            'title' => 'Judul Setelah Diupdate',
            'excerpt' => 'Excerpt updated',
            'image' => 'https://example.com/updated.jpg',
            'date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user)
                         ->put(route('dashboard.artikel.update', $artikel->slug), $data);

        $response->assertRedirect(route('dashboard.artikel.index'));
        
        $this->assertDatabaseHas('artikels', [
            'id' => $artikel->id,
            'title' => 'Judul Setelah Diupdate',
            'slug' => 'judul-setelah-diupdate',
        ]);
    }

    /** @test */
    public function it_can_delete_artikel()
    {
        $artikel = Artikel::factory()->create();

        $response = $this->actingAs($this->user)
                         ->delete(route('dashboard.artikel.destroy', $artikel->slug));

        $response->assertRedirect(route('dashboard.artikel.index'));
        $this->assertDatabaseMissing('artikels', ['id' => $artikel->id]);
    }
}