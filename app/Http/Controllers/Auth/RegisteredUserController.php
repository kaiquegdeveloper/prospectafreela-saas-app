<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $planName = $request->query('plan');
        $plan = null;
        
        if ($planName) {
            // Busca o plan pelo nome (case-insensitive)
            $plan = Plan::whereRaw('LOWER(name) = ?', [strtolower($planName)])
                ->where('is_active', true)
                ->first();
            
            // Se não existir, cria um novo plan com quotas padrão
            if (!$plan) {
                $plan = Plan::create([
                    'name' => $planName,
                    'monthly_prospect_quota' => 500,
                    'daily_prospect_quota' => 60,
                    'price' => 0,
                    'is_active' => true,
                ]);
            }
        }
        
        return view('auth.register', compact('plan'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'plan_name' => ['nullable', 'string', 'max:255'],
        ]);

        $planId = $request->input('plan_id');
        
        // Se não tiver plan_id mas tiver plan_name, busca ou cria o plan
        if (!$planId && $request->has('plan_name') && $request->filled('plan_name')) {
            $planName = $request->input('plan_name');
            
            // Busca o plan pelo nome (case-insensitive)
            $plan = Plan::whereRaw('LOWER(name) = ?', [strtolower($planName)])
                ->where('is_active', true)
                ->first();
            
            // Se não existir, cria um novo plan com quotas padrão
            if (!$plan) {
                $plan = Plan::create([
                    'name' => $planName,
                    'monthly_prospect_quota' => 500,
                    'daily_prospect_quota' => 60,
                    'price' => 0,
                    'is_active' => true,
                ]);
            }
            
            $planId = $plan->id;
        } elseif ($planId) {
            // Verifica se o plan existe e está ativo
            $plan = Plan::where('id', $planId)
                ->where('is_active', true)
                ->first();
            
            // Se o plan não for válido, define como null
            if (!$plan) {
                $planId = null;
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'plan_id' => $planId,
            // Se não tiver plan, as quotas padrão serão aplicadas via getEffectiveMonthlyQuota e getEffectiveDailyQuota
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Display the registration view for new clients from hashing (KIWIFY/HUBLA).
     * This route is used to identify payments through email matching.
     */
    public function createFromHashing(): View
    {
        return view('auth.register-hashing');
    }

    /**
     * Handle an incoming registration request from hashing.
     * This route is used to identify payments through email matching.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeFromHashing(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'plan_id' => ['nullable', 'exists:plans,id'],
            'plan_name' => ['nullable', 'string', 'max:255'],
        ]);

        $planId = $request->input('plan_id');
        
        // Se não tiver plan_id mas tiver plan_name, busca ou cria o plan
        if (!$planId && $request->has('plan_name') && $request->filled('plan_name')) {
            $planName = $request->input('plan_name');
            
            // Busca o plan pelo nome (case-insensitive)
            $plan = Plan::whereRaw('LOWER(name) = ?', [strtolower($planName)])
                ->where('is_active', true)
                ->first();
            
            // Se não existir, cria um novo plan com quotas padrão
            if (!$plan) {
                $plan = Plan::create([
                    'name' => $planName,
                    'monthly_prospect_quota' => 500,
                    'daily_prospect_quota' => 60,
                    'price' => 0,
                    'is_active' => true,
                ]);
            }
            
            $planId = $plan->id;
        } elseif ($planId) {
            // Verifica se o plan existe e está ativo
            $plan = Plan::where('id', $planId)
                ->where('is_active', true)
                ->first();
            
            // Se o plan não for válido, define como null
            if (!$plan) {
                $planId = null;
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'plan_id' => $planId,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
