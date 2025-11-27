<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $id
 * @property string $residence_id
 * @property \Illuminate\Support\Carbon $date
 * @property bool $is_available
 * @property numeric|null $price_override
 * @property int|null $min_stay
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Residence|null $residence
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar forDateRange($start, $end)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar forResidence($residenceId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar whereMinStay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar wherePriceOverride($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar whereResidenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AvailabilityCalendar whereUpdatedAt($value)
 */
	class AvailabilityCalendar extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $booking_number
 * @property int $user_id
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $country
 * @property string $residence_id
 * @property \Illuminate\Support\Carbon $check_in
 * @property \Illuminate\Support\Carbon|null $check_in_date
 * @property \Illuminate\Support\Carbon $check_out
 * @property \Illuminate\Support\Carbon|null $check_out_date
 * @property int $guests
 * @property int $guests_count
 * @property int $nights
 * @property numeric $price_per_night
 * @property numeric $total_price
 * @property numeric $subtotal_amount
 * @property numeric $tax_amount
 * @property numeric $final_amount
 * @property numeric $total_amount
 * @property string $status
 * @property string $payment_status
 * @property string|null $special_requests
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property \Illuminate\Support\Carbon|null $cancelled_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $payment_status_display
 * @property-read mixed $status_display
 * @property-read \App\Models\Payment|null $payment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Residence|null $residence
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $successfulPayments
 * @property-read int|null $successful_payments_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking confirmed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking dateRange($start, $end)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking forResidence($residenceId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking forUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereBookingNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCheckIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCheckInDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCheckOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCheckOutDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereFinalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereGuests($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereGuestsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereNights($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking wherePaymentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking wherePricePerNight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereResidenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereSpecialRequests($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereSubtotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereTaxAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking withoutTrashed()
 */
	class Booking extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module withoutTrashed()
 */
	class Module extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $lien_facebook
 * @property string|null $lien_instagram
 * @property string|null $lien_twitter
 * @property string|null $lien_linkedin
 * @property string|null $lien_tiktok
 * @property string|null $nom_projet
 * @property string|null $description_projet
 * @property string|null $contact1
 * @property string|null $contact2
 * @property string|null $contact3
 * @property string|null $email1
 * @property string|null $email2
 * @property string|null $localisation
 * @property string|null $google_maps
 * @property string|null $siege_social
 * @property string $mode_maintenance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereContact1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereContact2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereContact3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereDescriptionProjet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereEmail1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereEmail2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereGoogleMaps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereLienFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereLienInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereLienLinkedin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereLienTiktok($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereLienTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereLocalisation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereModeMaintenance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereNomProjet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereSiegeSocial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Parametre whereUpdatedAt($value)
 */
	class Parametre extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $booking_id
 * @property string $payment_reference
 * @property string $payment_method
 * @property numeric $amount
 * @property string $status
 * @property array<array-key, mixed>|null $payment_details
 * @property string|null $transaction_id
 * @property \Illuminate\Support\Carbon|null $processed_at
 * @property string|null $failure_reason
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Booking|null $booking
 * @property-read mixed $method_display
 * @property-read mixed $status_display
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment byMethod($method)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment successful()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereProcessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment withoutTrashed()
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property int $residence_type_id
 * @property string $description
 * @property string|null $full_description
 * @property int $capacity
 * @property numeric $price_per_night
 * @property array<array-key, mixed>|null $amenities
 * @property string $address
 * @property numeric|null $latitude
 * @property numeric|null $longitude
 * @property bool $is_available
 * @property bool $is_featured
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AvailabilityCalendar> $availabilityCalendar
 * @property-read int|null $availability_calendar_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read mixed $type_display
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ResidenceImage> $images
 * @property-read int|null $images_count
 * @property-read \App\Models\ResidenceImage|null $primaryImage
 * @property-read \App\Models\ResidenceType $residenceType
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence available()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence byCapacity($capacity)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence byType($typeId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence featured()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence priceRange($min = null, $max = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereAmenities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereFullDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence wherePricePerNight($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereResidenceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Residence withoutTrashed()
 */
	class Residence extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $residence_id
 * @property string $image_path
 * @property string|null $alt_text
 * @property bool $is_primary
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $full_url
 * @property-read \App\Models\Residence|null $residence
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceImage whereAltText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceImage whereIsPrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceImage whereResidenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceImage whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceImage whereUpdatedAt($value)
 */
	class ResidenceImage extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $description
 * @property int $min_capacity
 * @property int $max_capacity
 * @property bool $is_active
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $display_name
 * @property-read mixed $formatted_capacity
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Residence> $residences
 * @property-read int|null $residences_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType findSimilarSlugs(string $attribute, array $config, string $slug)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType ordered()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType whereMaxCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType whereMinCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ResidenceType withUniqueSlugConstraints(\Illuminate\Database\Eloquent\Model $model, string $attribute, array $config, string $slug)
 */
	class ResidenceType extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Booking[] $bookings
 * @property-read int|null $bookings_count
 * @method \Illuminate\Database\Eloquent\Relations\HasMany bookings()
 * @property int $id
 * @property string|null $username
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $city
 * @property string|null $country
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $avatar
 * @property string|null $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $activeBookings
 * @property-read int|null $active_bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $completedBookings
 * @property-read int|null $completed_bookings_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

