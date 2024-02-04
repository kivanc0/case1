// // MatchScheduleView.jsx

// import React, { useState, useEffect } from 'react';
// import { Head } from '@inertiajs/react';
// import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
// import MatchList from './MatchList'; 

// const MatchScheduleView = ({ auth }) => {
//   const [matches, setMatches] = useState([]);

//   useEffect(() => {
//   }, []); 

//   return (
//     <AuthenticatedLayout user={auth.user} header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Match Schedule</h2>}>
//       <Head title="Match Schedule" />
//       <div className="py-12">
//         <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
//           {/* Match list */}
//           <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
//             <MatchList matches={matches} />
//           </div>
//         </div>
//       </div>
//     </AuthenticatedLayout>
//   );
// };

// export default MatchScheduleView;
