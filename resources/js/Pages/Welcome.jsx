import Front from '@/Layouts/FrontLayout';
import { Link, Head } from '@inertiajs/react';

export default function Welcome({ auth, laravelVersion, phpVersion }) {
    return (

        <Front
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
                <Head title="Welcome" />
                <div
                    className="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-gray-100 dark:bg-gray-900 selection:bg-red-500 selection:text-white"
                    style={{ backgroundImage: "url('/images/welcome.jpg')", backgroundSize: "cover", backgroundPosition: "center" }}
                >
                    <div className="sm:fixed sm:top-0 sm:right-0 p-6 text-end">
                        {auth.user ? (
                            <Link
                                href={route('dashboard')}
                                className="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                            >
                                Dashboard
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={route('login')}
                                    className="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                                >
                                    Log in
                                </Link>

                                {/* <Link
                                    href={route('register')}
                                    className="ms-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500"
                                >
                                    Register
                                </Link> */}
                            </>
                        )}
                    </div>

                    <div className="max-w-7xl mx-auto p-6 lg:p-8">




                        <div className="flex justify-center mt-16 px-6 sm:items-center sm:justify-between">
                            <div className="text-center text-sm sm:text-start">&nbsp;</div>
                            <div className="flex flex-col items-center justify-center sm:justify-end text-sm text-gray-500 dark:text-gray-400 sm:ms-0">
                                <img src="/images/chimp-zee-logo.png" alt="Logo" className="h-auto w-44 me-2" />
                                <span className='text-center text-sm sm:text-start text-yellow-500'>Laravel v{laravelVersion} (PHP v{phpVersion})</span>
                            </div>
                        </div>
                    </div>
                </div>


        </Front>
    );
}
