<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use MongoDB\BSON\ObjectId;
use sammaye\Flash\Support\Flash;

class UserController extends Controller
{

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('user.edit')
            ->with(['model' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User                $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if ($request->input('action') === 'update_subscriptions') {
            $validator = Validator::make($request->all(),
                [
                    'email_frequency' => [
                        'required',
                        'string',
                        Rule::in(array_keys($user->getEmailFrequencies())),
                    ],
                    'comic_subs' => [
                        'required',
                        'array',
                        function($attribute, $value, $fail) {
                            foreach($value as $item) {
                                try {
                                    $item = new ObjectId($item);
                                } catch(\Throwable $e) {
                                    $fail(__('Invalid value provided for subscriptions'));
                                }
                            }
                        },
                    ],
                ],
                [],
                [
                    'comic_subs' => __('comic subscriptions'),
                ]
            );

            if ($validator->fails()) {
                return redirect()
                    ->route('user.edit', ['user' => $user])
                    ->withInput()
                    ->withErrors($validator);
            }

            $data = $validator->validated();
            $request->user()->fill($data)->modifyComics($data['comic_subs'])->save();

            Flash::success(__('Subscriptions Updated'));
            return redirect()
                ->route('user.edit', ['user' => $user]);
        }
        if ($request->input('action') === 'update_password') {
            $validator = Validator::make($request->all(), [
                'old_password' => [
                    'required',
                    'string',
                    'min:8',
                    function ($attribute, $value, $fail) use ($user) {
                        if (!Hash::check($value, $user->getAuthPassword())) {
                            $fail(__('Incorrect password'));
                        }
                    },
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                ],
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->route('user.edit', ['user' => $user])
                    ->withInput()
                    ->withErrors($validator);
            }

            $data = $validator->validated();

            $request->user()->forceFill([
                'password' => Hash::make($data['password']),
            ])->save();

            Flash::success(__('Password Updated'));
            return redirect()
                ->route('user.edit', ['user' => $user]);
        }

        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                'string',
                'regex:/^[0-9A-Za-z_]+$/',
                'max:255',
                Rule::unique('users')->ignore($user->id, '_id')
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id, '_id')
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('user.edit', ['user' => $user])
                ->withInput()
                ->withErrors($validator);
        }

        $data = $validator->validated();

        if ($data['email'] !== $user->email) {
            $request->user()->forceFill([
                'email' => $data['email'],
                'email_verified_at' => null
            ]);
        }

        $request->user()->forceFill([
            'username' => $data['username'],
        ])->save();

        Flash::success(__('Account Details Updated'));
        return redirect()
            ->route('user.edit', ['user' => $user]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User                $user
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Request $request, User $user)
    {
        $user->delete();

        return redirect()
            ->route('home');
    }
}
