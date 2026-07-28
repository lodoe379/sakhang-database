import React from 'react';

export default function Login({ canResetPassword, status }) {
    return (
        <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <h1 className="text-3xl font-bold text-gray-900 mb-6">Staff Authentication</h1>
            </div>

            <div className="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {status && (
                    <div className="mb-4 font-medium text-sm text-green-600">
                        {status}
                    </div>
                )}

                <form>
                    <div>
                        <label className="block font-medium text-sm text-gray-700">Email</label>
                        <input
                            type="email"
                            className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div className="mt-4">
                        <label className="block font-medium text-sm text-gray-700">Password</label>
                        <input
                            type="password"
                            className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div className="flex items-center justify-end mt-4">
                        <button className="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Log in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
