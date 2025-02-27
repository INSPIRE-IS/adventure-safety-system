import React from "react";
import { Link } from "@inertiajs/react";

const Pagination = ({ links }) => {
    return (
        <nav className="flex flex-col mb-96 lg:mb-2 lg:flex-row justify-between mt-4  lg:w-[60%] mx-auto">
            {links?.map((link, index) => (
                <Link
                    key={index}
                    href={link.url}
                    className={`px-3 py-2 rounded ${
                        link.active
                            ? "bg-[#F6C546] button-text1"
                            : "bg-[#F6C546] button-text1"
                    }`}
                    dangerouslySetInnerHTML={{ __html: link.label }}
                ></Link>
            ))}
        </nav>
    );
};

export default Pagination;
