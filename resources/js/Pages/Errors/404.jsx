import React from 'react';

export default function Error404() {
    return (
        <div className="flex items-center justify-center min-h-screen bg-gray-100">
            <div className="text-center">
            <h1 className="text-6xl font-bold text-red-500">404</h1>
                <p className="mt-4 text-xl text-gray-700 pb-3">
                    The page you are looking for could not be found
                </p>
                <a
                    href="/dashboard"
                    className="inline-block mt-6 px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600"
                >
                    Return to Dashboard
                </a>
            </div>
        </div>
    );
}
