<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Files
 *
 * @property int $id
 * @property int $video_id
 * @property int $filetype_id
 * @property string $filename
 * @property string $desc
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Filetype $filetype
 * @property-read \App\Models\Video $video
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files whereFiletypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files whereVideoId($value)
 * @mixin \Eloquent
 * @property-read mixed $download_url
 * @property-read mixed $viewer_url
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Files query()
 */
class Files extends \Eloquent {
	protected $guarded = [ 'id' ];
	protected $with = [ 'filetype' ];
	protected $appends = ['viewer_url','download_url'];

	public static function boot()
    {
        parent::boot();

        static::deleted(function($file)
        {
            if(is_file($file->full_path())) {
            	unlink($file->full_path());
            }
        });
    }

	public function video() {
		return $this->belongsTo('App\Models\Video');
	}

	public function filetype() {
		return $this->hasOne('App\Models\Filetype', 'id', 'filetype_id');
	}

	public function path() {
		return "uploads/video_" . $this->video_id . "/" . $this->filename;
	}

	public function url() {
		if($this->filetype->type == 'code') {
			return route('file_viewer', ['file_id' => $this->id]);
		} elseif($this->filetype->ext == 'stl') {
			return route('stl_viewer', ['file_id' => $this->id]);
		} elseif($this->filetype->ext == 'doc' OR
		   $this->filetype->ext == 'docx' OR
		   $this->filetype->ext == 'xls' OR
		   $this->filetype->ext == 'xlsx') {
		   	return 'https://view.officeapps.live.com/op/view.aspx?src=' . urlencode(url($this->path()));
		} else {
		 	return url($this->path());
		}
	}

	public function getViewerUrlAttribute() {
		return $this->url();
	}

	public function getDownloadUrlAttribute() {
		return url($this->path());
	}

	public function full_path() {
		return public_path() . DIRECTORY_SEPARATOR . $this->path();
	}

	public function just_filename() {
	    return basename($this->filename, '.' . $this->filetype->ext);
	}

	public function rename($newFilename)
	{
	    // Validate the filename
	    if(preg_match('/[^a-zA-Z0-9_. -]/', $newFilename) OR empty($newFilename)) {
	        return false;
	    }
	    $new_path = join( DIRECTORY_SEPARATOR, [
	                public_path(),
	                "uploads",
	                "video_" . $this->video_id,
	                $newFilename . '.' . $this->filetype->ext
	                ]);
	    if(rename($this->full_path(), $new_path)) {
	        $this->filename = $newFilename . '.' . $this->filetype->ext;
	        $this->save();
	        return true;
	    } else {
	        return false;
	    }
	}
}