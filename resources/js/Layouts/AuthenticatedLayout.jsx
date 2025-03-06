import { useState } from 'react';
import Header from '@/Components/Header';
import Sidebar from '@/Components/Sidebar';
import Footer from '@/Components/Footer';
import FlashMessage from '@/Components/FlashMessage';



export default function Authenticated({ user, header, children }) {
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);
// w-full md:w-3/4 lg:w-1/2 xl:w-1/3
    return (
        // <div className="flex w-full min-h-screen  bg-gray-100">
        //     <Header />
        //     <Sidebar />

        //     <main>{children}</main>
        //     <Footer />
        // </div>

        <div className="flex flex-col w-full min-h-screen bg-white">
        <Header  />
        <div className="lg:flex lg:flex-1 lg:overflow-hidden bg-[#F8F8F8]">
            {/* Sidebar */}
            <Sidebar />
            <div className="w-[22px] bg-[#F8F8F8]"></div>
            {/* Main Content */}
            <FlashMessage />
            <main className="lg:flex-1 lg:overflow-auto mr-[72px] ">
                {children}
            </main>

        </div>
        <Footer />
                    {/* <form  id="logoutform" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>         */}
        </div>

    );
}
