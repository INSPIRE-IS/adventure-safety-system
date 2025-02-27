




<div className="hidden lg:grid grid-cols-3 gap-4 text-gray-700 font-semibold mb-2">
<div className="col-span-1">Name</div>
<div className="col-span-1">Role</div>
<div className="col-span-1">Access</div>
</div>


const TableCard = ({ staff }) => {
    return (
    <>
        <div className="hidden md:grid grid-cols-3 gap-4 text-gray-700 font-semibold mb-2">
        <div className="col-span-1">{staff?.name}</div>
        {staff?.roles.map((role) => {
            const permissions = role?.permissions;
            return (
                < >
                    <div key={role.id} className="bg-gray-200 text-gray-600 rounded-full px-2 py-1 text-xs col-span-1">{role.name}</div>
                    {/* {permissions?.map((permission) => (
                        <div key={permission.id} className="bg-gray-200 text-gray-600 rounded-full px-2 py-1 text-xs col-span-1">{permission.name}</div>
                    ))} */}
                </>
            );
        })}
        </div>


        <div className="flex md:hidden flex-col lg:flex-row justify-between items-center bg-white shadow-md p-4 rounded-lg">
            <div className="font-semibold text-lg col-span-1" ></div>
            {staff?.roles.map((role) => (
                <div key={role.id} className="bg-gray-200 text-gray-600 rounded-full px-2 py-1 text-xs col-span-1">{role.name}</div>
            ))}
            {/* {staff?.roles?.[0].permissions.map((permission) => (
                <div key={permission.id} className="bg-gray-200 text-gray-600 rounded-full px-2 py-1 text-xs col-span-1">{permission.name}</div>
            ))} */}
        </div>
    </>    
    );
};

export default TableCard;
