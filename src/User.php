<?php

  namespace DGtek\Models;

  use Illuminate\Auth\Notifications\ResetPassword;
  use Illuminate\Contracts\Auth\MustVerifyEmail;
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Foundation\Auth\User as Authenticatable;
  use Illuminate\Notifications\Notifiable;
  use Laravel\Cashier\Billable;
  use Laravel\Sanctum\HasApiTokens;
  use Spatie\Permission\Traits\HasRoles;

  class User extends Authenticatable implements MustVerifyEmail {
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Billable;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable
      = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'is_active',
        'is_email_verified',
        'trial_ends_at',
      ];

    /**
     * The attributes that should be hidden for serialization.
     * @var array<int, string>
     */
    protected $hidden
      = [
        'password',
        'remember_token',
      ];

    public $preventsLazyLoading = false;
    /**
     * The attributes that should be cast.
     * @var array<string, string>
     */
    protected $casts
      = [
        'email_verified_at' => 'datetime',
        'trial_ends_at'     => 'datetime',
      ];

    public function userSubscriptions()
    {
      return $this->belongsToMany(SubscriptionTier::class, 'user_subscription', 'user_id', 'subscription_id')->withPivot('start_date', 'end_date', 'is_active', 'id', 'method', 'method_id', 'start_date', 'end_date', 'is_canceled', 'cancel_date')->withTimestamps();
    }

    // Function provide active subscription only from pivot table =user_subscription
    public function activeSubscriptions()
    {
      return $this->belongsToMany(SubscriptionTier::class, 'user_subscription', 'user_id', 'subscription_id')->withPivot('is_active', 'id', 'method', 'method_id', 'start_date', 'end_date', 'is_canceled', 'cancel_date')->wherePivot('is_active', 1);

    }

    /**
     * Get the folders for the  user.
     */
    public function folders()
    {
      return $this->hasMany(Folder::class);
    }

    /**
     * Get the share sent for the  user.
     */
    public function shareSent()
    {
      return $this->hasMany(Share::class, 'sender_user_id');
    }

    /**
     * Get the share received sent for the  user.
     */
    public function shareReceived()
    {
      return $this->hasMany(Share::class, 'recipient_user_id');
    }

    /**
     * Get the yoflos for the  user.
     */
    public function yoflos()
    {
      return $this->hasMany(Yoflo::class);
    }

    /**
     * Get the nodes for the  user.
     */
    public function nodes()
    {
      return $this->hasMany(Node::class);
    }

    /**
     * Get the libraries for the  user.
     */
    public function files()
    {
      return $this->hasMany(File::class);
    }

    /**
     * Get the Shares for the  user.
     */
    public function shares()
    {
      return $this->hasMany(Share::class, 'sender_user_id', 'id');
    }

    /**
     * Get the customer name that should be synced to Stripe.
     * @return string|null
     */
    public function stripeName()
    {
      return $this->first_name;
    }

    /**
     * Get the payments for the  user.
     */
    public function payments()
    {
      return $this->hasMany(Payment::class);
    }

  }
