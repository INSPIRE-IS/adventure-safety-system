import { useState } from 'react';
import Header from '@/Components/Header';
import Sidebar from '@/Components/Sidebar';
import Footer from '@/Components/Footer';
import FlashMessage from '@/Components/FlashMessage';



export default function Authenticated({ user, is_admin, header, children }) {
    console.log('user', user);
    console.log('header', header);
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);
    return (

        <div className="flex flex-col w-full min-h-screen bg-white">
        <Header  />
        <div className="lg:flex lg:flex-1 lg:overflow-hidden bg-[#F8F8F8]">
            {/* Sidebar */}
            {is_admin && (<Sidebar />)}
            
            <div className="w-[22px] bg-[#F8F8F8]"></div>
            {/* Main Content */}
            <FlashMessage />
            <main className="lg:flex-1 lg:overflow-auto mr-[72px] ">
                {children}
            </main>

        </div>
        <Footer />
        </div>

    );
}
