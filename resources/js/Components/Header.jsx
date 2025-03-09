import React, { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import lookupIcon from '@/Utils/lookupIcons';

const Header = () => {
  const { auth } = usePage().props;
  console.log('auth header', auth);
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const { url } = usePage(); // Get the current URL

  const navLinks = [
    { name: 'Staff Management', route: 'users.index', permission: "user_access" },
    { name: 'Inspection Management', route: 'inspections.index', permission: "inspection_access" },
    { name: 'PPE Management', route: 'equipments.index', permission: "inspection_access" },
    { name: 'Safety Management', route: 'drills.index', permission: "drill_access" },
    { name: 'Incident Management', route: 'incidents.index', permission: "incident_access" },
    { name: 'Complaint Management', route: 'complaints.index', permission: "file_access" },
    { name: 'Site Management', route: 'files.index', permission: "file_access" },
  ];

  const handleLogout = () => {
    router.post(route('logout'));
};

  return (
    <header className="bg-white w-[95%] ml-[50px] mt-[23px] mb-[23px] mr-[50px] h-[60px] flex items-center justify-between px-10">
      {/* Left Section: Logo */}

      <div className="flex items-center space-x-2">
      <Link href="/dashboard">
        <img className="w-[62px] h-auto" src="./images/logo.png" alt="" />
      </Link>
      </div>

      {/* Hamburger Menu for Mobile */}
      <button
        className="lg:hidden text-gray-700 focus:outline-none"
        onClick={() => setIsMenuOpen(!isMenuOpen)}
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          className="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            strokeLinecap="round"
            strokeLinejoin="round"
            strokeWidth={2}
            d="M4 6h16M4 12h16m-7 6h7"
          />
        </svg>
      </button>

      {/* Center Section: Navigation Links */}
      {isMenuOpen ? <NavMenu isMenuOpen={isMenuOpen} navLinks={navLinks} url={url} auth={auth} /> : <NavTop isMenuOpen={isMenuOpen} navLinks={navLinks} url={url} auth={auth} />}

      {/* Right Section: Icons */}
      <div className="hidden lg:flex items-center space-x-4">
        <button onClick={handleLogout} className="w-[45px] h-[45px] bg-[#c6c7cb] rounded-full flex items-center justify-center hover:bg-gray-200">
          {/* <img src="./images/logout-icon.svg" alt="Logout" className="w-[20px] h-[20px] rounded-full" /> */}
          <div className=' w-[20px] h-[20px] rounded-full flex items-center justify-center hover:bg-gray-200'>
                  {lookupIcon("logout")}
          </div>
        </button>
        <button className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">
          {/* <img src="./images/user-icon.svg" alt="User Icon" className="w-45 h-45" /> */}
          <div className=' w-[45px] h-[45px] rounded-full flex items-center justify-center hover:bg-gray-200'>
                  {lookupIcon("user")}
          </div>
        </button>
        <button className="w-[45px] h-[45px] bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200">
          {/* <img src="./images/notification-icon.svg" alt="Notification Icon" className="w-45 h-45" /> */}
          <div className=' w-[45px] h-[45px] rounded-full flex items-center justify-center hover:bg-gray-200'>
                  {lookupIcon("notification")}
          </div>
        </button>
      </div>
    </header>
  );
};

export default Header;

const NavTop = ({isMenuOpen, navLinks, url, auth}) => {
  return (
    <nav
      className={`${
        isMenuOpen ? 'block' : 'hidden'
      } absolute top-[60px] left-0 w-full bg-white shadow-lg lg:static lg:block lg:shadow-none`}
    >
      <ul className="flex flex-col lg:flex-row lg:items-center lg:justify-center lg:space-x-8 text-gray-800 text-sm font-semibold h-full">
        {navLinks.map((link) => {
          if (link?.permission && !auth?.permissions?.includes(link.permission)) {
            return null;
          }
          return(
          <li key={link.name} className="header-links">
            <Link
              href={route(link.route)}
              className={`hover:text-blue-500 cursor-pointer ${
                url === link.route ? 'text-blue-500 font-bold' : ''
              }`}
            >
              {link.name.split(' ').map((word, index) => (
                <React.Fragment key={index}>
                  {word}
                  <br />
                </React.Fragment>
              ))}
            </Link>
          </li>
        )})}
      </ul>
    </nav>
  );
}

const NavMenu = ({isMenuOpen, navLinks, url, auth}) => {
  return (
    <nav
      className={`${
        isMenuOpen ? 'block' : 'hidden'
      } absolute top-[60px] pt-2 pl-10 left-0 w-full bg-white shadow-lg lg:static lg:block lg:shadow-none`}
    >
      <ul className="flex flex-col lg:flex-row lg:items-center lg:justify-center lg:space-x-8 text-gray-800 text-sm font-semibold h-full">
        {navLinks.map((link) => {
          if (link?.permission && !auth?.permissions?.includes(link.permission)) {
            return null;
          }
          return(
          <li key={link.name} className="header-links mt-2 hover:text-blue-500 cursor-pointer">
            <Link
              href={route(link.route)}
              className={`hover:text-blue-500 cursor-pointer ${
                url === link.route ? 'text-blue-500 font-bold' : ''
              }`}
            >
              {link.name.split(' ').map((word, index) => (
                <React.Fragment key={index}>
                  {word}
                  <br />
                </React.Fragment>
              ))}
            </Link>
          </li>
        )})}
        <li className="header-links mt-2 hover:text-blue-500 cursor-pointer">
            Profile Management
          </li>
          <li className="header-links my-2 hover:text-blue-500 cursor-pointer">
            Message Management
          </li>
      </ul>
    </nav>
  );
}

const NavTop1 = ({isMenuOpen}) => {
  return (
      <nav
        className={`${
          isMenuOpen ? 'block' : 'hidden'
        } absolute top-[60px] left-0 w-full bg-white shadow-lg lg:static lg:block lg:shadow-none`}
      >
        <ul className="flex flex-col lg:flex-row lg:items-center lg:justify-center lg:space-x-8 text-gray-800 text-sm font-semibold h-full">
          <li className="header-links hover:text-blue-500 cursor-pointer">
            Staff <br/>Management
          </li>
          <li className="header-links hover:text-blue-500 cursor-pointer">
            Inspection <br/>Management
          </li>
          <li className="header-links hover:text-blue-500 cursor-pointer">
            PPE <br/>Management
          </li>
          <li className="header-links hover:text-blue-500 cursor-pointer">
            Safety <br/>Management
          </li>
          <li className="header-links hover:text-blue-500 cursor-pointer">
            Incident <br/>Management
          </li>
          <li className="header-links hover:text-blue-500 cursor-pointer">
            Complaint <br/>Management
          </li>
          <li className="header-links hover:text-blue-500 cursor-pointer">
            Site <br/>Management
          </li>
        </ul>
      </nav>
  );

}

const NavMenu1 = ({isMenuOpen}) => {
  return (
      <nav
        className={`${
          isMenuOpen ? 'block' : 'hidden'
        } absolute top-[60px] pt-2 pl-10 left-0 w-full bg-white shadow-lg lg:static lg:block lg:shadow-none`}
      >
        <ul className="flex flex-col lg:flex-row lg:items-center lg:justify-center lg:space-x-8 text-gray-800 text-sm font-semibold h-full">
          <li className="header-links mt-2 hover:text-blue-500 cursor-pointer">
            Staff Management
          </li>
          <li className="header-links mt-2 hover:text-blue-500 cursor-pointer">
            Inspection Management
          </li>
          <li className="header-links mt-2 hover:text-blue-500 cursor-pointer">
            PPE Management
          </li>
          <li className="header-links mt-2 hover:text-blue-500 cursor-pointer">
            Safety Management
          </li>
          <li className="header-links mt-2 hover:text-blue-500 cursor-pointer">
            Incident Management
          </li>
          <li className="header-links mt-2 hover:text-blue-500 cursor-pointer">
            Complaint Management
          </li>
          <li className="header-links mt-2 hover:text-blue-500 cursor-pointer">
            Site Management
          </li>
          <hr className="border-gray-200 my-2" />
          <li className="header-links mt-2 hover:text-blue-500 cursor-pointer">
            Profile Management
          </li>
          <li className="header-links my-2 hover:text-blue-500 cursor-pointer">
            Message Management
          </li>
        </ul>
      </nav>
  );

}