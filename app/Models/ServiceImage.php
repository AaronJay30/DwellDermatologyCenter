<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ServiceImage extends Model
{
	use HasFactory;

	protected $fillable = [
		'service_id',
		'image_path',
		'display_order',
	];

	public function service(): BelongsTo
	{
		return $this->belongsTo(Service::class);
	}

	/**
	 * Get the full URL for the image
	 */
	public function getUrlAttribute(): string
	{
		if (empty($this->image_path)) {
			return '';
		}

		// Try to get URL using Storage facade (preferred method)
		try {
			// Check if file exists in storage
			if (Storage::disk('public')->exists($this->image_path)) {
				return Storage::disk('public')->url($this->image_path);
			}
		} catch (\Exception $e) {
			// If Storage fails, fall through to asset()
		}

		// Fallback to asset() - this works if storage symlink is set up correctly
		// Remove any leading slashes from image_path to avoid double slashes
		$cleanPath = ltrim($this->image_path, '/');
		return asset('storage/' . $cleanPath);
	}
}


