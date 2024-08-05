import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { PageProps } from '@/types';
import { useEffect, useRef, useState } from 'react';
import axios from 'axios';
import { BallTriangle, FallingLines } from 'react-loader-spinner';
import { DataTable } from 'primereact/datatable';
import { Column } from 'primereact/column';
import { Button } from 'primereact/button';
import { SplitButton } from 'primereact/splitbutton';
import { Toast } from 'primereact/toast';
import { SpeedDial } from 'primereact/speeddial';
import { useParams } from 'react-router-dom';

interface Establishemt{
    id: number;
    nameEtablishment: string;
    latitude: string;
    longitude: string;
    address: string;
    pos: string;
    numberPos: string;
    workers: JSON;
    workingDays: JSON;
    isOnDemonstration: boolean;
    isActive: boolean;
    subscriptionExpiryDate: string;
    settings: JSON;
    user_id: number;
}

export default function Establishemt({ auth}: PageProps) {

    const { id } = useParams();
    const [data, setData] = useState<Establishemt[]>([]);
    const [currentPage, setCurrentPage] = useState<number>(1);
    const [isLoading, setIsLoading] = useState<boolean>(true);
    const toast = useRef<Toast>(null);

    console.info(id);

    useEffect(() => {

        axios.get(`/api/v1.1/admin/establishments/${currentPage}`, {
            headers: {
                "Authorization" : "Bearer " + localStorage.getItem("token"),
            }
        })        
            .then(response => {
                setData(response.data.data.data);
                setIsLoading(false);
            })
            .catch(error => {
                console.error('There was an error fetching the data!', error);
                setIsLoading(false);
        });
    }, []);

    const columns = [
        {field: 'id', field_action: 'id', header: 'id'},
        {field: 'nameEtablishment', field_action: 'nameEtablishment', header: 'nameEtablishment'},
        {field: 'address', field_action: 'address', header: 'address'},
        {field: 'latitude', field_action: 'latitude', header: 'latitude'},
        {field: 'longitude', field_action: 'longitude', header: 'longitude'},
        {field: 'numberPos', field_action: 'numberPos', header: 'Active'},
        {field: 'subscriptionExpiryDate', field_action: 'subscriptionExpiryDate', header: "Date d'expiration de l'abonnement"},
        {field: 'id', field_action: 'action', header: "Actions"},
    ];

    const header = (
        <div className="flex flex-wrap align-items-center justify-content-between gap-2">
            <span className="text-xl text-900 font-bold">Etablissements</span>
            {/* <Button icon="pi pi-refresh" rounded raised /> */}
        </div>
    );

    const items = [
        {
            label: 'Update',
            icon: 'pi pi-refresh',
            command: () => {
                toast.current.show({ severity: 'success', summary: 'Updated', detail: 'Data Updated' });
            }
        },
        {
            label: 'Delete',
            icon: 'pi pi-times',
            command: () => {
                toast.current.show({ severity: 'warn', summary: 'Delete', detail: 'Data Deleted' });
            }
        },
        {
            label: 'React Website',
            icon: 'pi pi-external-link',
            command: () => {
                window.location.href = 'https://reactjs.org/';
            }
        },
        {
            label: 'Upload',
            icon: 'pi pi-upload',
            command: () => {
                //router.push('/fileupload');
            }
        }
    ];    
    const save = () => {
        toast.current.show({ severity: 'success', summary: 'Success', detail: 'Data Saved' });
    };

    const BtnAction = (idEstablishment: any) => {
        return <div className="card flex justify-content-center">
            <Link
                href={`/establishment/${idEstablishment.id}`}
            >
                <Button icon="pi pi-eye" className='hover:text-red-700' />
                {/* <Toast ref={toast}></Toast>
                {/* <SplitButton label="Save" icon="pi pi-plus" onClick={save} model={items} /> */}                    
                {/* <SpeedDial model={items} direction="up" transitionDelay={80} showIcon="pi pi-bars" hideIcon="pi pi-times" buttonClassName="p-button-outlined" /> */}
            </Link>
        </div>
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Etablissement</h2>}
        >
            <Head title="Etablissement" />

            
            <div className="">
                {isLoading ? (
                    <>
                        <div className="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
                            <div className=" ">
                            <BallTriangle
                                height="105"
                                width="105"
                                color="#CA0B4A"
                                ariaLabel="loading"
                            />
                            </div>{" "}
                        </div>
                        <div className="fixed inset-0 z-40 bg-primary"></div>
                    </>
                ) : (
                    <div className="">
                        <div className="bg-white p-4">
                            <div className="p-6 text-gray-900">
                                <div>
                                     <div className="card shadow-sm bg-blue-700">
                                        <DataTable value={data} tableStyle={{ minWidth: '50rem' }}  header={header} className='bg-blue-700'>
                                            <Column field="id" header="ID"></Column>
                                            <Column field="nameEtablishment" header="Nom de l'etablissement"></Column>
                                            <Column field="address" header="Adresse"></Column>
                                            <Column field="latitude" header="Latitude"></Column>
                                            <Column field="longitude" header="Longitude"></Column>
                                            <Column field="numberPos" header="Nbr P.O.S"></Column>
                                            <Column field="subscriptionExpiryDate" header="Date d'expiration de l'abonnement"></Column>
                                            <Column body={BtnAction} header="Actions"></Column> 
                                            {/* {columns.map((col, index) => (
                                                col.field_action == "action" ?
                                                <Column body={BtnAction} header="Actions"></Column>  :
                                                <Column key={col.field} field={col.field} header={col.header} />
                                            ))} */}
                                        </DataTable>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
