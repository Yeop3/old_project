describe('Home Page', () => {
  it('has the correct title and heading', () => {
    cy.visit('/')
    cy.title().should('equal', 'Log in | Minton - Vue js Admin Template')
  })
})
