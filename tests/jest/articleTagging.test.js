/**
 * articleTagging.test.js
 * This file contains tests for the articleTagging Vue component.
 */
const { mount } = require( '@vue/test-utils' );
// Mock global.mw object used for accessing MediaWiki's global variables and services.
global.mw = {
    config: {
        get: jest.fn().mockImplementation( ( key ) => {
            const configMap = {
                wgAdiutorArticleTagging: {
                    tagList: [
                        {
                            label: 'Label1',
                            tags: [
                                { tag: 'Tag1', description: 'Description1' }
                            ]
                        }
                    ]
                },
                wgPageName: 'Mock_Page_Name'
            };
            return configMap[ key ];
        } )
    },
    message: jest.fn().mockImplementation( () => ( {
        text: jest.fn().mockReturnValue( 'MockMessage' )
    } ) ),
    Api: jest.fn( () => ( {
        postWithToken: jest.fn( () => Promise.resolve() ),
        get: jest.fn( () => Promise.resolve() )
    } ) ),
    notify: jest.fn(),
    msg: jest.fn( () => 'translated message' )
};

const ArticleTagging = require( '../../resources/ext.adiutor/components/articleTagging.vue' );

describe( 'ArticleTagging', () => {
    it( 'initializes with default values', () => {
        const wrapper = mount( ArticleTagging );
        expect( wrapper.vm.openTagDialog ).toBe( true );
        expect( wrapper.vm.primaryAction.label ).toBe( 'translated message' );
    } );

    it( 'filters tag list based on search term', () => {

        const tagList = [
            {
                label: 'Label1',
                tags: [
                    {
                        tag: 'Tag1',
                        description: 'Description1',
                        items: []
                    },
                    {
                        tag: 'Tag2',
                        description: 'Description2',
                        items: []
                    }
                ]
            },
            {
                label: 'Label2',
                tags: [
                    {
                        tag: 'Tag3',
                        description: 'Description3',
                        items: []
                    },
                    {
                        tag: 'Tag4',
                        description: 'Description4',
                        items: []
                    }
                ]
            }
        ];

        global.mw.config.get = jest.fn( () => ( {
            wgAdiutorArticleTagging: {
                tagList: tagList
            }
        } ) );

    } );

    it( 'notifies when tagArticle is called with no selected tags', async () => {
        const wrapper = mount( ArticleTagging );

        await wrapper.vm.tagArticle();

        expect( mw.notify ).toHaveBeenCalledWith( 'translated message', {
            title: 'translated message',
            type: 'error'
        } );
    } );

    it( 'creates an API request when tagArticle is called', async () => {

        const postWithTokenMock = jest.fn( () => Promise.resolve() );
        mw.Api = jest.fn( () => ( {
            postWithToken: postWithTokenMock
        } ) );

        const wrapper = mount( ArticleTagging );

        wrapper.vm.selectedTags = [
            {
                tag: 'Tag1',
                description: 'Description1',
                items: []
            }
        ];

        await wrapper.vm.tagArticle();

    } );

} );
