<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardVenue;
use App\Models\Subscription;
use App\Models\Package;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        if (!session('setup-card')) {
            abort(403, 'You can not register without Card!');
        }
        return view('auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:255', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (session('setup-card')) {
                $card = Card::where('uuid', session('setup-card'))->first();
                if ($card) {
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'phone' => $data['phone'],
                        'password' => Hash::make($data['password']),
                        'role' => 'venue',
                    ]);

                    // create venue and attach card
                    $venue = Venue::create(['user_id' => $user->id, 'name' => now()]);
                    CardVenue::create(['card_id' => $card->id, 'venue_id' => $venue->id]);

                    $card->update(['status' => \App\Enums\CardStatusEnum::attached]);
                    // activate trial
                    $package = Package::where('name', operator: 'premium')->where('type', 'month')->first();
                    Subscription::create([
                        'venue_id' => $venue->id,
                        'package_id' => $package->id,
                        'status' => true
                    ]);
                    return $user;
                }
            }

            throw new \Exception('Card setup failed.');
        });
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));
        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect()->route('venues.edit', $user->firstVenue()->first()->slug);
    }
}